<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\ProfileResources;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;

/**
 * @group Profile Management
 *
 * APIs to manage the user profile
 */
class ProfileController extends BaseController
{
    /**
     * Display User profile.
     *
     * Get user profile
     *
     * Must be authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function show() {

        $profile = Profile::all()->where('user_id',Auth::id())->first();
        return $this->sendResponse(new ProfileResources($profile),'User Profile');
    }

    /**
     * Update user profile.
     *
     * Must be authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'password' => 'required|min:8',
            'gender' => 'required|in:male,female' ,
            'city' => 'required',
            'bio'=> 'required' ,
        ]);

        if ($validator->fails()) {
            return $this->sendError('Please validate error', $validator->errors());
        }
        $user=User::find(Auth::id());
        $user->name=$request->name;
        $user->password= Hash::make($request->password);
        $user->profile->gender=$request->gender;
        $user->profile->city=$request->city;
        $user->profile->bio=$request->bio;
        $user->profile->update();
        $user->update();
        $profile = Profile::all()->where('user_id',Auth::id())->first();
        return $this->sendResponse(new ProfileResources($profile),'User Profile');
    }

}
