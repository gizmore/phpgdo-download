<?php
namespace GDO\Download\Test;

use GDO\Core\GDT_Method;
use GDO\Download\GDO_Download;
use GDO\Download\GDO_DownloadVote;
use GDO\Download\Method\Crud;
use GDO\Download\Method\File;
use GDO\Download\Module_Download;
use GDO\Tests\GDT_MethodTest;
use GDO\Tests\Module_Tests;
use GDO\Tests\TestCase;
use GDO\UI\GDT_Page;
use GDO\Votes\Method\Up;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertMatchesRegularExpression;

/**
 * Download also tests payment and voting.
 *
 * @author gizmore
 */
final class DownloadTest extends TestCase
{

	public function testUpload()
	{
		$path = Module_Tests::instance()->filePath(
			'data/upload.txt');
		$this->fakeFileUpload('dl_file', 'upload.txt', $path);
		$m = Crud::make();
		$p = [
			'dl_title' => 'Upload test',
			'dl_info' => 'A test file for upload',
			'dl_level' => '0',
		];
		$r = GDT_Method::make()->method($m)->inputs($p);
		$r = $r->execute('create');
		$r->renderHTML();
		$this->assertOK('Test if plaintext can be uploaded');

		$path = Module_Tests::instance()->filePath(
			'data/v8.mp3');
		$this->fakeFileUpload('dl_file', 'intro.mp3', $path);
		$m = Crud::make();
		$p = [
			'dl_title' => 'RanzIntro',
			'dl_info' => 'Some punk music',
			'dl_level' => '1',
		];
		GDT_Method::make()->method($m)->inputs($p)->execute('create');

		assertEquals(2, GDO_Download::table()->countWhere(), 'Test upload of an mp3 file.');
	}

	public function testVoting()
	{
		# Vote
		$dl = GDO_Download::findById('1');
		$m = Up::make();
		$p = [
			'gdo' => GDO_DownloadVote::table()->gdoClassName(),
			'id' => $dl->getID(),
			'rate' => '5',
		];
		GDT_MethodTest::make()->method($m)->getParameters($p)->execute();
		$this->assert200('Assert voting works');
		assertEquals(5, $dl->getVoteRating(), 'Assert vote outcome is 5 on download.');

		# Revote
		$m = Up::make();
		$p = [
			'gdo' => GDO_DownloadVote::table()->gdoClassName(),
			'id' => $dl->getID(),
			'rate' => '4',
		];
		GDT_MethodTest::make()->method($m)->getParameters($p)->execute();
		assertEquals(4, $dl->getVoteRating(), 'Assert vote outcome is 4 after revote.');

		# Blockers
		$this->userGaston();
		Module_Download::instance()->saveConfigVar('dl_vote_guest', '0');
		GDT_MethodTest::make()->method($m)->getParameters($p)->execute();
		assertMatchesRegularExpression('/Guests are not allowed/', GDT_Page::$INSTANCE->topResponse()->render(), 'Check Guest block.');

		Module_Download::instance()->saveConfigVar('dl_vote_guest', '1');
		GDT_MethodTest::make()->method($m)->getParameters($p)->execute();
		assertMatchesRegularExpression('/recently/', GDT_Page::$INSTANCE->topResponse()->render(), 'Check IP vote block.');
	}

	public function testDownload()
	{
		$dl = GDO_Download::findById('1');
		$m = File::make();
		$p = ['id' => $dl->getID()];
		GDT_MethodTest::make()->method($m)->getParameters($p)->execute();
		$this->assert200('Test if download does work.');
	}

//	public function testDelete() {}
//
//	public function testUnlock() {}
//
//	public function testPayment() {}

}
