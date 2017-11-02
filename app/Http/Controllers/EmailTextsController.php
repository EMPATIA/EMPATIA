<?php

namespace App\Http\Controllers;

use App\EmailText;
use App\One\One;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Mail;

/**
 * Class MailsController
 * @package App\Http\Controllers
 */
class EmailTextsController extends Controller
{

    protected $keysRequired = [
        'subject',
        'body',
        'tag'
    ];

    /**
     * Requests a list of mails.
     * Returns the list of mails.
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $emails = EmailText::all();

            foreach ($emails as $email) {
                if (!($email->translation($request->header('LANG-CODE')))) {
                    if (!$email->translation($request->header('LANG-CODE-DEFAULT')))
                        return response()->json(['error' => 'No translation found'], 404);
                }
            }

            return response()->json($emails, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Email list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Request a specific mail.
     * Returns the details of a specific mail.
     *
     * @param Request $request
     * @param $emailKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $emailKey)
    {
        try {
            $email = EmailText::whereEmailKey($emailKey)->firstOrFail();

            if (!($email->translation($request->header('LANG-CODE')))) {
                if (!$email->translation($request->header('LANG-CODE-DEFAULT')))
                    return response()->json(['error' => 'No translation found'], 404);
            }

            return response()->json($email, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Email not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Store a newly created mail in storage.
     * Returns the details of the newly created mail.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->keysRequired, $request);

        try {
            do {
                $rand = str_random(32);
                if (!($exists = EmailText::whereEmailKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $email = EmailText::create(
                [
                    'email_key' => $key,
                    'created_by' => $userKey
                ]
            );

            foreach ($request->json('translations') as $translation) {
                if (isset($translation['language_code']) && isset($translation['subject']) && isset($translation['body']) && isset($translation['tag'])) {
                    $email->emailTextTranslations()->create(
                        [
                            'language_code' => $translation['language_code'],
                            'subject'       => empty($translation['subject']) ? "" : $translation['subject'],
                            'body'          => empty($translation['body']) ? "" : $translation['body'],
                            'tag'           => empty($translation['tag']) ? "" : $translation['tag'],

                            'updated_by' => $userKey,
                            'created_by' => $userKey
                        ]
                    );
                }
            }


            return response()->json($email, 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to store new Email'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Update the mail in storage.
     * Returns the details of the updated mail.
     *
     * @param Request $request
     * @param $emailKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $mailKey
     */
    public function update(Request $request, $emailKey)
    {
        $userKey = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->keysRequired, $request);

        try {
            $mail = EmailText::whereEmailKey($emailKey)->firstOrFail();;

            foreach ($request->json('translations') as $translation) {
                if (isset($translation['language_code']) && isset($translation['title'])) {}
            }
            
            
            $mail->subject = $request->json('subject');
            $mail->body = $request->json('body');
            $mail->tag = $request->json('tag');
            $mail->updated_by = $userKey;
            $mail->save();

            return response()->json($mail, 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to update the Email'], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Email not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Remove the specified mail from storage.
     *
     * @param Request $request
     * @param $emailKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $mailKey
     */
    public function destroy(Request $request, $emailKey)
    {
        $userKey = ONE::verifyToken($request);

        try {
            $email = EmailText::whereEmailKey($emailKey)->firstOrFail();
            $email->deleted_by = $userKey;
            // Save who deleted
            $email->save();
            // Soft delete
            $email->delete();
            return response()->json('OK', 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to delete the Email'], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Email not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests a specific list of mails.
     * Returns the list of requested mails.
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listEmails(Request $request)
    {
        try {
            $mails = Mail::whereIn('email_key', $request->json('email_keys'))->get();
            return response()->json($mails, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
