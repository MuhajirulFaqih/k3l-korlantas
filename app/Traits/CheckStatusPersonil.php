<?php
namespace App\Traits;

use Illuminate\Support\Facades\DB;
use DateTime;

trait CheckStatusPersonil
{
	protected function CheckLoginPersonil($personil)
	{
		$check = DB::table('oauth_access_tokens')
				->where('user_id', ($personil->auth->id ?? 0))
				->count();
		$check2 = DB::table('oauth_access_tokens')
				->where('user_id', ($personil->bhabin->auth->id ?? 0))
				->count();
		return ($check > 0 || $check2 > 0) ? '1' : '0'; 
	}

	protected function CheckActivePersonil($personil)
	{
		$lama = new DateTime(date('Y-m-d', strtotime($personil->w_status_dinas)));
		$baru = new DateTime(date('Y-m-d'));
		$diff = $lama->diff($baru);
		return $diff->days >= 3 ? '0' : '1'; 
	}
}