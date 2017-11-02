<?php

namespace App\Http\Controllers;

use App\AccessAnalytics;
use App\AccessAnalyticsEntity;
use App\Entity;
use App\Site;
use App\User;
use Exception;
use Illuminate\Http\Request;

class UserAnalysisController extends Controller
{
    public function getUserAnalysis(Request $request)
    {
        try {
            $loggedData = $request->json('logged_data');

            $responseData = [];
            $response = [];
            $totalUsersLogged = [];
            $totalUsersNotLogged = [];
            $totalUsersByEntity = [];
            $totalUsersBySite = [];

            if (!empty($loggedData)) {

                foreach ($loggedData as $data) {
                    $log = json_decode($data);

                    if (!is_null($log->entity_key)) {
                        $entity = Entity::whereEntityKey($log->entity_key)->first();
                        if (!is_null($log->site_key)) {
                            $site = Site::where('key', $log->site_key)->first();

                            $user = is_null($log->user_key) ? null : User::whereUserKey($log->user_key)->first();

                            if (empty($response[$log->entity_key])) {
                                $response[$log->entity_key] = [
                                    'entity_name' => $entity->name,
                                    'sites' => array()
                                ];
                            }
                            if (empty($response[$log->entity_key]['sites'][$log->site_key])) {
                                $response[$log->entity_key]['sites'][$log->site_key] = array(
                                    'site_name' => $site->name,
                                    'users' => [
                                        'logged' => [],
                                        'not_logged' => []
                                    ]
                                );
                            }
                            if (!is_null($user)) {
                                $response[$log->entity_key]['sites'][$log->site_key]['users']['logged'][$log->user_key] = array(
                                    'user_key' => $log->user_key,
                                    'user_name' => $user->name,
                                    'php_session_id' => $log->phpsessionid,
                                    'time' => $log->timestamp,
                                    'ip' => $log->ip
                                );
                                $totalUsersLogged[] = $log->phpsessionid;
                            } else {
                                $response[$log->entity_key]['sites'][$log->site_key]['users']['not_logged'][] = array(
                                    'php_session_id' => $log->phpsessionid,
                                    'time' => $log->timestamp,
                                    'ip' => $log->ip
                                );
                                $totalUsersNotLogged[] = $log->phpsessionid;
                            }

                            $totalUsersByEntity[$log->entity_key][] = $log->phpsessionid;
                            $totalUsersBySite[$log->site_key][] = $log->phpsessionid;
                        }
                    }
                }
            }

            $totalUsers = count(array_unique(array_merge($totalUsersLogged, $totalUsersNotLogged)));

            $responseData['analysis_data'] = $response;
            $responseData['total_users'] = $totalUsers;
            $responseData['total_users_by_entity'] = $totalUsersByEntity;
            $responseData['total_users_by_site'] = $totalUsersBySite;

            return response()->json($responseData, 200);

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
            return response()->json(['error' => 'Failed to retrieve User Analysis Information'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->json('data');
            $accessAnalytics = AccessAnalytics::create([
                                    'total_users' =>  isset($data["total_users"]) ? $data["total_users"]: 0,
                                    'total_page_access' => !empty($data["total_page_access"]) ?$data["total_page_access"] : 0]);

            foreach($data["entities"] ?? [] as $key => $data) {
                $entity = Entity::whereEntityKey($key)->firstOrFail();
                AccessAnalyticsEntity::create([
                    'total_users' => !empty($data["total_users"]) ? $data["total_users"] : 0,
                    'total_page_access' => !empty($data["total_page_access"]) ? $data["total_page_access"] : 0,
                    'entity_id' => $entity->id,
                    'access_analytics_id' => $accessAnalytics->id
                ]);
            }

            return response()->json(true, 201);
        }
        catch(Exception $e){
            return response()->json(['error' => 'Failed to store User Analysis Information'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    public function getUserAnalysisStats(Request $request)
    {
        try {
            $data = [];
            $startDate = !empty( $request->get("start_date")) ?  $request->get("start_date") : "";
            $endDate = !empty( $request->get("end_date")) ?  $request->get("end_date") : "";
            $userAnalysisData = AccessAnalytics::where('created_at', ">=", $startDate)
                                ->where("created_at", "<=", $endDate)
                                ->with("accessAnalyticsEntity")
                                ->get();

            $start = new \Carbon\Carbon($startDate);
            $end = new \Carbon\Carbon($endDate);
            $days = $start->diff($end)->days;
            $minutes = $start->diffInMinutes($end);

            if( $minutes <= 120 ){
                $type = "minutes";
                $increment = 5;
            } else if( $minutes > 120 && $minutes <= 1440 ){
                $type = "minutes";
                $increment = round($minutes / 25,0);
            } else if($minutes > 1440 && $minutes <= 2880){
                $type = "hours";
                $increment = 1;
            } else if($minutes > 2880 && $minutes <= 10080) {
                $type = "hours";
                $increment = 2;
            }  else if($minutes > 10080 && $minutes <= 108000) {
                $type = "days";
                $increment = 1;
            } else {
                $type = "days";
                $increment = round($days / 25,0);
            }

            if($type == "days") {
                $days = $start->diff($end)->days;
                $i = 0;
                $data = [];
                // Initialising time stamps
                $date = new \Carbon\Carbon($startDate);
                $dateEndLapse = new \Carbon\Carbon( $date->format('Y-m-d H:i') );
                while($i <= $days)
                {
                    // Initialising Counters
                    $countTotalUsers = 0;
                    $countTotalPageAccess = 0;

                    // Increment for date interval
                    $dateEndLapse->addDays($increment);

                    foreach($userAnalysisData as $userAnalysisItem){
                        $created_at = new \Carbon\Carbon($userAnalysisItem->created_at);
                        if($created_at->format('Y-m-d') >= $date->format('Y-m-d') && $created_at->format('Y-m-d') <= $dateEndLapse->format('Y-m-d')){
                            $countTotalUsers += !empty($userAnalysisItem->total_users) ? $userAnalysisItem->total_users : 0;
                            $countTotalPageAccess += !empty($userAnalysisItem->total_users) ? $userAnalysisItem->total_page_access : 0;
                            // Counting data for entities
                            foreach(!empty($userAnalysisItem->accessAnalyticsEntity) ? $userAnalysisItem->accessAnalyticsEntity :[] as $entityItem){
                                $entity = Entity::whereId($entityItem->entity_id)->firstOrFail();
                                $data[ $date->format('Y-m-d')]["entities"][$entity->entity_key]["name"] = $entity->name;
                                $data[ $date->format('Y-m-d')]["entities"][$entity->entity_key]["total_users"] = isset($data[ $date->format('Y-m-d')]["entities"][$entityItem->entity_id]["total_users"]) ?  $data[ $date->format('Y-m-d')]["entities"][$entityItem->entity_id]["total_users"]+$entityItem->total_users : $entityItem->total_users;
                                $data[ $date->format('Y-m-d')]["entities"][$entity->entity_key]["total_page_access"] = isset($data[ $date->format('Y-m-d')]["entities"][$entityItem->entity_id]["total_page_access"]) ?  $data[ $date->format('Y-m-d')]["entities"][$entityItem->entity_id]["total_page_access"]+$entityItem->total_page_access : $entityItem->total_page_access;
                            }
                        }
                    }

                    // Storing general data
                    $data[$date->format('Y-m-d')]["total_users"] = $countTotalUsers;
                    $data[$date->format('Y-m-d')]["total_page_access"] = $countTotalPageAccess;

                    // Increment current timestamp
                    $i = $i + $increment;
                    $date->addDays($increment);
                }
            } elseif( $type == "hours"){
                // Initialising time stamps
                $date = new \Carbon\Carbon($startDate);
                $dateEndLapse = new \Carbon\Carbon( $date->format('Y-m-d H:i') );
                while($dateEndLapse->format('Y-m-d H') <= $end->format('Y-m-d H'))
                {
                    // Initialising Counters
                    $countTotalUsers = 0;
                    $countTotalPageAccess = 0;

                    // Increment for date interval
                    $dateEndLapse->addHours($increment);

                    foreach($userAnalysisData as $userAnalysisItem){
                        $created_at = new \Carbon\Carbon($userAnalysisItem->created_at);
                        if($created_at->format('Y-m-d H') >= $date->format('Y-m-d H') && $created_at->format('Y-m-d H') <= $dateEndLapse->format('Y-m-d H')){
                            $countTotalUsers      += !empty($userAnalysisItem->total_users) ? $userAnalysisItem->total_users : 0;
                            $countTotalPageAccess += !empty($userAnalysisItem->total_page_access) ? $userAnalysisItem->total_page_access : 0;
                            foreach(!empty($userAnalysisItem->accessAnalyticsEntity) ? $userAnalysisItem->accessAnalyticsEntity :[] as $entityItem){
                                $entity = Entity::whereId($entityItem->entity_id)->firstOrFail();
                                // Storing entity data
                                $data[ $date->format('Y-m-d')]["entities"][$entity->entity_key]["name"] = $entity->name;
                                $data[ $date->format('Y-m-d')]["entities"][$entity->entity_key]["total_users"] = isset($data[ $date->format('Y-m-d H')]["entities"][$entityItem->entity_id]["total_users"]) ?  $data[ $date->format('Y-m-d H')]["entities"][$entityItem->entity_id]["total_users"]+$entityItem->total_users : $entityItem->total_users;
                                $data[ $date->format('Y-m-d')]["entities"][$entity->entity_key]["total_page_access"] = isset($data[ $date->format('Y-m-d H')]["entities"][$entityItem->entity_id]["total_page_access"]) ?  $data[ $date->format('Y-m-d H')]["entities"][$entityItem->entity_id]["total_page_access"]+$entityItem->total_page_access : $entityItem->total_page_access;
                            }
                        }
                    }

                    // Storing general data
                    $data[ $date->format('Y-m-d H:00')]["total_users"] = $countTotalUsers;
                    $data[ $date->format('Y-m-d H:00')]["total_page_access"] = $countTotalPageAccess;

                    // Increment current timestamp
                    $date->addHours($increment);
                }
            } elseif( $type == "minutes") {
                $start = new \Carbon\Carbon($startDate);
                $minutes = $start->diffInMinutes($end);
                $i = 0;
                // Initialising time stamps
                $date = new \Carbon\Carbon($startDate);
                $dateEndLapse = new \Carbon\Carbon( $date->format('Y-m-d H:i') );
                while($i <= $minutes)
                {
                    $countTotalUsers = 0;
                    $countTotalPageAccess = 0;

                    // Increment for date interval
                    $dateEndLapse->addHours($increment);

                    foreach($userAnalysisData as $userAnalysisItem){
                        $created_at = new \Carbon\Carbon($userAnalysisItem->created_at);
                        if($created_at->format('Y-m-d H:i') >= $date->format('Y-m-d H:i') && $created_at->format('Y-m-d H:i') <= $dateEndLapse->format('Y-m-d H:i')){
                            $countTotalUsers      += !empty($userAnalysisItem->total_users) ? $userAnalysisItem->total_users : 0;
                            $countTotalPageAccess += !empty($userAnalysisItem->total_page_access) ? $userAnalysisItem->total_page_access : 0;
                            // Counting data for entities
                            foreach(!empty($userAnalysisItem->accessAnalyticsEntity) ? $userAnalysisItem->accessAnalyticsEntity :[] as $entityItem){
                                $entity = Entity::whereId($entityItem->entity_id)->firstOrFail();
                                $data[ $date->format('Y-m-d H:i')]["entities"][$entity->entity_key]["name"] = $entity->name;
                                $data[ $date->format('Y-m-d H:i')]["entities"][$entity->entity_key]["total_users"] = isset($data[ $date->format('Y-m-d H:i')]["entities"][$entityItem->entity_id]["total_users"]) ?  $data[ $date->format('Y-m-d H:i')]["entities"][$entityItem->entity_id]["total_users"]+$entityItem->total_users : $entityItem->total_users;
                                $data[ $date->format('Y-m-d H:i')]["entities"][$entity->entity_key]["total_page_access"] = isset($data[ $date->format('Y-m-d H:i')]["entities"][$entityItem->entity_id]["total_page_access"]) ?  $data[ $date->format('Y-m-d H:i')]["entities"][$entityItem->entity_id]["total_page_access"]+$entityItem->total_page_access : $entityItem->total_page_access;
                            }
                         }
                    }

                    // Storing general data
                    $data[ $date->format('Y-m-d H:i') ]["total_users"] = $countTotalUsers;
                    $data[ $date->format('Y-m-d H:i') ]["total_page_access"] = $countTotalPageAccess;

                    // Increment current timestamp
                    $date->addMinute($increment);
                    $i = $i + $increment;
                }
            }


            return response()->json(["data" => $data, "type" => $type ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve User Analysis Information'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
