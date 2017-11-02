<?php

namespace App\Http\Controllers;

use App\NewsletterSubscription;
use App\One\One;
use App\Site;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class NewsletterSubscriptionsController extends Controller
{
    protected $keysRequired = [
        'email',
        'active'
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {
        try{
            $site = Site::where('key',$request->header('X-SITE-KEY'))->firstOrFail();

            $query = $site->newsletterSubscriptions()->whereSiteId($site->id);

            $recordsTotal = $query->count();
            $tableData = $request->input('tableData') ?? null;

            $query = $query->orderBy($tableData['order']['value'], $tableData['order']['dir']);

            if(!empty($tableData['search']['value'])) {
                $query = $query
                    ->where('newsletter_subscription_key', 'like', '%'.$tableData['search']['value'].'%')
                    ->orWhere('email', 'like', '%'.$tableData['search']['value'].'%');
            }

            $recordsFiltered = $query->count();

            $newsletterSubscriptions = $query
                ->skip($tableData['start'])
                ->take($tableData['length'])
                ->get();

            $data['subscriptions'] = $newsletterSubscriptions;
            $data['recordsTotal'] = $recordsTotal;
            $data['recordsFiltered'] = $recordsFiltered;

            return response()->json($data, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Newsletter Subscriptions not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Newsletter Subscriptions'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $newsletterSubscriptionKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $newsletterSubscriptionKey) {
        try{
            $site = Site::where('key',$request->header('X-SITE-KEY'))->firstOrFail();

            $newsletterSubscription = $site->newsletterSubscriptions()->whereNewsletterSubscriptionKey($newsletterSubscriptionKey)->firstOrFail();

            return response()->json($newsletterSubscription, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Newsletter Subscription not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Newsletter Subscription'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        ONE::verifyKeysRequest($this->keysRequired, $request);

        try{
            $site = Site::where('key',$request->header('X-SITE-KEY'))->firstOrFail();

            do {
                $rand = str_random(32);
                $key = "";

                if (!($exists = NewsletterSubscription::whereNewsletterSubscriptionKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $newsletterSubscription = $site->newsletterSubscriptions()->create(
                [
                    'newsletter_subscription_key'   =>  $key,
                    'email'                         =>  $request->json('email'),
                    'active'                        =>  $request->json('active')
                ]
            );
            return response()->json($newsletterSubscription, 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to store new Newsletter Subscription'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $newsletterSubscriptionKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $newsletterSubscriptionKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);
        try{
            $site = Site::where('key',$request->header('X-SITE-KEY'))->firstOrFail();
            $newsletterSubscription = $site->newsletterSubscriptions()->whereNewsletterSubscriptionKey($newsletterSubscriptionKey)->firstOrFail();

            $newsletterSubscription->email  = $request->json('email');
            $newsletterSubscription->active = $request->json('active');
            $newsletterSubscription->save();

            return response()->json($newsletterSubscription, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Newsletter Subscription not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Newsletter Subscription'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $newsletterSubscriptionKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $newsletterSubscriptionKey)
    {
        ONE::verifyToken($request);
        try{
            $site = Site::where('key',$request->header('X-SITE-KEY'))->firstOrFail();
            $newsletterSubscription = $site->newsletterSubscriptions()->whereNewsletterSubscriptionKey($newsletterSubscriptionKey)->firstOrFail();
            $newsletterSubscription->delete();

            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Newsletter Subscription not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Newsletter Subscription'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    public function export(Request $request) {
        try{
            $site = Site::where('key',$request->header('X-SITE-KEY'))->firstOrFail();

            $newsletterSubscriptions = $site->newsletterSubscriptions()->select(["email","active","created_at"])->get();

            return response()->json($newsletterSubscriptions, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Newsletter Subscriptions not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Newsletter Subscriptions'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
