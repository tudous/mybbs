<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Topic;
use App\Models\User;
use App\Models\Link;

class CategoriesController extends Controller
{
    public function show(Request $request,Topic $topic,Category $category,User $user,Link $link){

    	$topics=$topic->withOrder($request->order)
    				->where('category_id',$category->id)
    				->paginate(20);
    	 $active_users=$user->getActiveUsers();
         $links=$link->getAllCache();
    	return view('topics.index',compact('category','topics','active_users','links'));
	}

}
