<?php
namespace GDO\Download\Method;

use GDO\Core\GDO;
use GDO\DB\Query;
use GDO\Download\GDO_Download;
use GDO\Download\Module_Download;
use GDO\File\GDO_File;
use GDO\Table\GDT_Table;
use GDO\Table\MethodQueryList;
use GDO\User\GDO_User;

/**
 * Download overview.
 *
 * @version 6.10.4
 * @since 3.0.0
 * @author gizmore
 */
final class FileList extends MethodQueryList
{

	public function getMethodTitle(): string
	{
		$count = GDO_Download::countDownloads();
		return t('link_downloads', [$count]);
	}

	protected function setupTitle(GDT_Table $table): void
	{
		$count = GDO_Download::countDownloads();
		$table->title('link_downloads', [$count]);
	}

	public function isGuestAllowed(): string
	{
		return Module_Download::instance()->cfgGuestDownload();
	}

	public function onRenderTabs(): void
	{
		Module_Download::instance()->renderTabs();
	}

	public function gdoTable(): GDO
	{
		return GDO_Download::table();
	}

	public function getQuery(): Query
	{
		$userid = GDO_User::current()->getID();
		return GDO_Download::table()->select('gdo_download.*, dl_file_t.*, v.vote_value own_vote')->
		joinObject('dl_file')->
		join("LEFT JOIN gdo_downloadvote v ON v.vote_user = $userid AND v.vote_object = dl_id")->
		where('dl_deleted IS NULL AND dl_accepted IS NOT NULL');
	}

	public function gdoHeaders(): array
	{
		$gdo = GDO_Download::table();
		$file = GDO_File::table();
		return [
// 			GDT_EditButton::make(),
// 			$gdo->gdoColumn('dl_id'),
			$gdo->gdoColumn('dl_title'),
			$file->gdoColumn('file_size'),
			$gdo->gdoColumn('dl_downloads'),
			$gdo->gdoColumn('dl_price'),
			$gdo->gdoColumn('dl_votes'),
			$gdo->gdoColumn('dl_rating'),
// 			GDT_Button::make('view'),
		];
	}

}
