<?php

namespace App\Http\Controllers\Frontend\Empatia\Cbs;

use App\Helpers\HFrontend;
use App\Models\Empatia\Cbs\Cb;
use App\Models\Empatia\Cbs\Topic;
use App\Models\Empatia\Cbs\Vote;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Jenssegers\Agent\Agent;
use Modules\Backend\Entities\Configuration;
use Modules\Files\Facades\Disk;
use Modules\Frontend\Helpers\CbsHelpers;
use Symfony\Component\HttpFoundation\StreamedResponse;


class CbsController extends Controller
{
    public static $cbActions = ["show", "result", "thank-you-vote", "thank-you-topic"];
    public static $topicActions = ["show", "create", "edit"];

    // TODO: temporarilly placed function; too generic to remain in this controller
    public static function getProjectPath()
    {
        $projectAssetsPath  = config('one.project_path', '');
        $projectPath        = str_replace('/', '.', $projectAssetsPath);

        return $projectPath;
    }

    public static function showCbType($type)
    {
        $slug = request()->input('slug');
        $params = request()->input('params');
        $projectPath = self::getProjectPath();

        return view("frontend.$projectPath.cbs.$type.index", compact('type', 'slug', 'params'));
    }

    public static function showCb($cb, $params)
    {
        /*  Validations  */
//    $isAuthRequired = getField(CbHelpers::getConfig($cb, 'login_required'), 'active') == 'on';
//    $userHasResponded   = $cb->topics->where('created_by', auth()->id())->first();
//    if( ($isAuthRequired && !auth()->user()) || $userHasResponded ){
//        header("Location: " . route('page', [ CbHelpers::getCbTypeSlug( getField($cb, 'type') ) , getField($cb, 'slug.'.getLang(), '') ]), true, 302);
//        exit();
//    }

        $params = explode('/', $params);
        $projectPath = self::getProjectPath();
        $viewPrefix = "frontend.$projectPath.cbs.$cb->type.";
        $moments = self::getPhaseMoments($cb);
        $lastParam = $params[array_key_last($params)];
        $content = null;

        if ($lastParam == "show")
            return view("{$viewPrefix}cb.show", compact('cb', 'moments', 'content'));

        elseif ($lastParam == "result")
            return view("{$viewPrefix}cb.result", compact('cb', 'moments', 'content'));

        elseif (in_array($lastParam, ['thank-you-vote', 'thank-you-topic'])) {
            $content = HFrontend::getContentByCode("$cb->type-$lastParam");

            if (empty($content)) {
                return view("{$viewPrefix}cb.$lastParam", compact('cb', 'moments', 'content'));
            }

            return view("frontend.$projectPath.cms.page", [
                'content' => $content,
                'previousSlug' => "$cb->type/{$cb->slug->{getLang()}}"
            ]);

            return view("{$viewPrefix}cb.thank-you", compact('cb', 'moments', 'content'));
        } else
            return view("{$viewPrefix}cb.show", compact('cb', 'moments', 'content'));

    }

    public static function showTopic(Cb $cb, ?Topic $topic, $params)
    {
        $projectPath = self::getProjectPath();
        $viewPrefix = "frontend.$projectPath.cbs.$cb->type.";

        $params = explode('/', $params);
        $urlAction = $params[array_key_last($params)];
        $urlAction = in_array($urlAction, self::$topicActions) ? $urlAction : 'show';

        // TODO: clean this mess; find a way to deal with this neatly

        if( !$cb->isTopicActionAuthorized($urlAction) ){
            // intended url was set for redirect
            if( session()->has('url.intended') && is_string(session('url.intended')) && request()->path() != session('url.intended') ){
                return redirect( session('url.intended') );
            }
            // user is not authenticated
            if( empty( auth()?->user() ) ){
                app('redirect')->setIntendedUrl( request()->path() );
                return redirect()->route('keycloak.login');
            } else {
                // TODO: deal with this case better
                abort(403);
            }
        }

        $content = null;

        if ($urlAction == "create")
            return view("{$viewPrefix}topic.form", ['cb' => $cb, 'action' => 'create', 'content' => $content]);

        elseif ($urlAction == "edit" && !empty($topic))
            return view("{$viewPrefix}topic.form", ['cb' => $cb, 'topic' => $topic, 'action' => 'edit', 'content' => $content]);

        elseif ($urlAction == "show" && !empty($topic))
            return view("{$viewPrefix}topic.show", ['cb' => $cb, 'topic' => $topic, 'content' => $content]);

        else
            return view("{$viewPrefix}topic.show", ['cb' => $cb, 'topic' => $topic, 'content' => $content]);
    }

    public static function __callStatic(string $name, array $arguments)
    {
        // TODO: Implement __callStatic() method.
    }

    public function topicBySlug($slug)
    {
        $previous = $next = "";
        $current = Topic::where('slug->' . getLang(), $slug)->first();
        $topics = Topic::where('cb_id', $current->cb_id)->get();
        foreach ($topics as $key => $topic) {
            if ($topics[$key]->id == $current->id) {
                if ($key == 0) {
                    $previous = null;
                    $next = $topics[$key + 1];
                } elseif ($key == count($topics) - 1) {
                    $previous = $topics[$key - 1];
                    $next = null;
                } else {
                    $previous = $topics[$key - 1];
                    $next = $topics[$key + 1];
                }
            }
        }

        return view('frontend::cbs.topic-show', ['topic' => $current, 'previous' => $previous, 'next' => $next]);
    }


    public static function getPhaseMoments(Cb $cb)
    {
        $phaseGroups = ['topic', 'vote'];
        $now = Carbon::now();
        $moments = [];

        foreach (getField($cb, 'data.configurations', []) as $phaseGroupKey => $phaseGroup) {
            if (!in_array($phaseGroupKey, $phaseGroups)) {
                continue;
            }

            foreach ($phaseGroup ?? [] as $phaseCode => $phase) {
                $phaseStart = carbon(getField($phase, 'start_date'));
                $phaseEnd = carbon(getField($phase, 'end_date'));

                $moments["{$cb->type}_before_{$phaseGroupKey}_$phaseCode"] = !empty($phaseStart) && $now < $phaseStart;
                $moments["{$cb->type}_during_{$phaseGroupKey}_$phaseCode"] = !empty($phaseStart) && $now >= $phaseStart && (empty($phaseEnd) || $now < $phaseEnd);
                $moments["{$cb->type}_after_{$phaseGroupKey}_$phaseCode"] = !empty($phaseEnd) && $now >= $phaseEnd;
            }
        }
        return $moments;
    }


    // AJAX methods
    public function getVotesIds(Request $request)
    {
        $cb = CbsHelpers::getCb($request->slug);
        return Vote::where('event_id', $cb->code)->where('user_id', auth()->user()->id)->where('details->submitted', false)->get()->pluck('id');
    }

    public function getTopicsIds(Request $request)
    {
        $cb = CbsHelpers::getCb($request->slug);
        return Topic::where('cb_id', $cb->id)->get()->pluck('id');
    }

    public function getVotes(Request $request)
    {
//        dd($request);
        $ids = $request->votesIds;
        if (count($ids) > 0) {
            $topicId = Vote::whereIn('id', $ids)->get()->pluck('topic_id');
            $topicsVoted = Topic::whereIn('id', $topicId)->get();
            foreach ($topicsVoted as $key => $voted) {
                foreach ($voted->parameters as $parameter) {
                    if ($parameter->type == "image" && $parameter->code == "cover_img") {
                        if (!empty($parameter->value))
                            $topicsVoted[$key]['cover_img'] = \Disk::getImage($parameter->value[0]->id)['url'];
                        else
                            $topicsVoted[$key]['cover_img'] = asset('assets/img/portugalparticipa_default-img.jpg');
                    }
                }
            }
            return $topicsVoted;
        } else
            return "votes empty";
    }

    public function vote(Request $request)
    {
        if (!\Modules\Frontend\Helpers\CbsHelpers::isProfileComplete('user_parameters'))
            return "profile incomplete";
        $voted = Vote::withTrashed()->where('user_id', auth()->user()->id)->where('topic_id', $request->topicId)->orWhere('event_id', '==', $request->eventId)->get()->first();

        if (CbsHelpers::getAvailableVotes($request->slug) == 0 && ($voted == null || $voted->deleted_at != null)) {
            return "no available votes";
        }
        if ($voted == null) {
            $source = $this->getSource(new Agent());
            $vote = Vote::create([
                'user_id' => auth()->user()->id,
                'topic_id' => $request->topicId,
                'event_id' => 'code',
                'value' => 1,
                'source' => $source,
                'details' => (object)['submitted' => false],
            ]);
            if ($vote == null)
                return "error creating vote";
            return $vote->id;
        } else {
            if ($voted->details->submitted)
                return "cant change submitted votes";
            if ($voted->deleted_at == null) {
                $voted->delete();
            } else {
                $voted->update([
                    'source' => $this->getSource(new Agent())
                ]);
                $voted->restore();
            }
            return $voted->id;
        }
    }

    public function submit(Request $request)
    {
        $ids = $request->votes;
        foreach ($ids as $voteId) {
            $vote = Vote::where('id', $voteId)->get()->first();
            $vote->update([
                'details' => (object)['submitted' => true]
            ]);
        }
    }

    private function getSource($agent): string
    {
        if ($agent->isDesktop())
            return "Desktop";
        elseif ($agent->isMobile())
            return "Mobile";
        elseif ($agent->isTablet())
            return "Tablet";

        return 'Unknown';
    }

    public function getTopic(Request $request)
    {
        $images = [];
        $videos = [];
        $files = [];
        $topic = Topic::where('id', $request->id)->first()->only('id', 'title', 'content', 'slug', 'parameters');
        foreach ($topic['parameters'] as $parameter) {
            if ($parameter->type == 'image') {
                if ($parameter->code == 'cover_img')
                    if (!empty($parameter->value))
                        $images['cover_img'] = \Disk::getImage($parameter->value[0]->id)['url'];
                    else
                        $images['cover_img'] = asset('assets/img/portugalparticipa_default-img.jpg');
                else {
                    foreach ($parameter->value as $key => $imgs) {
                        $images[$key] = \Disk::getImage($imgs->id)['url'];
                    }
                }
            }
            if ($parameter->type == 'file') {
                if ($parameter->code == 'video') {
                    foreach ($parameter->value as $key => $v) {
                        $video = Disk::get($v->id);

                        if ($video instanceof \Exception) {
                            $videos[$key] = "File not found";
                        } else
                            $videos[$key] = $video['url'];
                    }
                } else {
                    foreach ($parameter->value as $key => $f) {
                        $file = Disk::get($f->id);

                        if ($file instanceof \Exception) {
                            $files[$key] = "File not found";
                        } else
                            $files[$key] = $file['url'];
                    }
                }
            }
        }
        if (!array_key_exists("cover_img", $images)) {
            $images['cover_img'] = asset('assets/img/portugalparticipa_default-img.jpg');
        }
        $topic['images'] = $images;
        $topic['videos'] = $videos;
        $topic['files'] = $files;
        return $topic;
    }

    public function getTopicImages(Request $request)
    {
        $images = [];
        foreach ($request->imgs as $image) {
            if ($image['code'] == "cover_img") {
                $images['cover_img'] = \Disk::getImage($image['value'][0]['id'])['url'];
            } else {
                foreach ($image['value'] as $key => $imgs) {
                    $images[$key] = \Disk::getImage($imgs['id'])['url'];
                }
            }
        }
        if (!array_key_exists("cover_img", $images)) {
            $images['cover_img'] = asset('assets/img/portugalparticipa_default-img.jpg');
        }
        return $images;
    }


    /////////

    public function voteInPerson($slug)
    {
        if (Cb::where('slug->' . getLang(), $slug)->firstOrFail()) {
            if (CbsHelpers::getCbVoteInPerson($slug)) {
                return view('frontend::cbs.vote-in-person.vote-in-person', ['slug' => $slug]);
            } else {
                logError("Cb has vote_in_person set to false");
                abort(404);
            }
        }
    }

    public function verifyNIF(Request $request)
    {
        $nif = $request->nif;
        $slug = $request->slug;

        //TODO:PRECORRE TODOS OS USERS À PROCURA DE UM QUE TENHA O NIF QUE RECEBEU

        $user = CbsHelpers::getUserWithNIF($nif);

        if ($user == null) {
            $configs = Configuration::where('code', 'user_parameters')->first()->configurations;
//
            $params = [];
            foreach ($configs as $key => $value) {
                if ($key == 'tax_id')
                    $params[$key] = (string)$nif;
                else
                    $params[$key] = null;
            }
////            //TODO: NIF VALIDO, CRIA USER
            $user = User::create([
                'name' => 'vote-in-person_' . $nif,
                'email' => 'vote-in-person_' . $nif,
                'parameters' => (object)$params
            ]);

            $user->save();
        }

        Auth::login($user);

//        return "Utilizador " . $user->email . " autenticado com sucesso.";

        $votes = [
            'available' => CbsHelpers::getAvailableVotes($slug),
            'used' => CbsHelpers::getUsedVotes($slug),
            'allSubmitted' => CbsHelpers::allSubmitted($slug)
        ];

        return json_encode($votes);

    }

    public function inPerson_getVotes(Request $request)
    { //Cant use normal getVotes because i need the votes not the topics votted
        $cb = CbsHelpers::getCb($request->slug);

        return Vote::select('topic_id', 'details')->where('event_id', $cb->code)->where('user_id', auth()->user()->id)->get();
    }

    public function refreshToken(Request $request)
    {
        session()->regenerate();
        return response()->json([
            "token" => csrf_token()],
            200);

    }
}
