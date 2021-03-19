<?php
namespace App\Traits;

trait AudioKejadian
{
	protected function audioKejadian($kejadian)
	{
		$kejadian = strtolower($kejadian);
		if(preg_match('/\bpenculikan\b/', $kejadian) == 1) {
			return 'penculikan';
		} elseif(preg_match('/\bbegal\b/', $kejadian) == 1) {
			return 'begal';
		} elseif(preg_match('/\blakalantas\b/', $kejadian) == 1 || preg_match('/\bkecelakaan\b/', $kejadian) == 1) {
			return 'lakalantas';
		} elseif(preg_match('/\bpembunuhan\b/', $kejadian) == 1) {
			return 'pembunuhan';
		} elseif(preg_match('/\bpemerasan\b/', $kejadian) == 1) {
			return 'pemerasan';
		} elseif(preg_match('/\bpemerkosaan\b/', $kejadian) == 1) {
			return 'pemerkosaan';
		} elseif(preg_match('/\bpenculikan\b/', $kejadian) == 1) {
			return 'penculikan';
		} elseif(preg_match('/\bpencurian motor\b/', $kejadian) == 1 || preg_match('/\bcuranmor\b/', $kejadian) == 1) {
			return 'curanmor';
		} elseif(preg_match('/\bpenganiayaan\b/', $kejadian) == 1) {
			return 'penganiayaan';
		} elseif(preg_match('/\bpengerusakan\b/', $kejadian) == 1) {
			return 'pengerusakan';
		} elseif(preg_match('/\bperampasan\b/', $kejadian) == 1) {
			return 'perampasan';
		} elseif(preg_match('/\bperampokan\b/', $kejadian) == 1) {
			return 'perampokan';
		} elseif(preg_match('/\bperjudian\b/', $kejadian) == 1) {
			return 'perjudian';
		} elseif(preg_match('/\bkebakaran\b/', $kejadian) == 1) {
			return 'kebakaran';
		} elseif(preg_match('/\bsenpi\b/', $kejadian) == 1 || preg_match('/\bsenapan api\b/', $kejadian) == 1) {
			return 'senpi';
		} else {
			return 'kejadian';
		}
	}
}