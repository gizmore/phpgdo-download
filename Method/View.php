<?php
namespace GDO\Download\Method;

use GDO\Core\GDO;
use GDO\Core\GDO_Error;
use GDO\Core\GDT;
use GDO\Core\GDT_Response;
use GDO\Download\GDO_Download;
use GDO\Download\Module_Download;
use GDO\UI\GDT_CardView;
use GDO\UI\MethodCard;
use GDO\User\GDO_User;

/**
 * View a download for downloading or purchase.
 *
 * @version 7.0.1
 * @since 3.1.0
 * @author gizmore
 */
final class View extends MethodCard
{

	public function gdoTable(): GDO
	{
		return GDO_Download::table();
	}

	public function onRenderTabs(): void
	{
		$module = Module_Download::instance();
		$module->renderTabs();
	}

	public function getMethodTitle(): string
	{
		$dl = $this->getDownload();
		return t('mt_download_view', [$dl->displayTitle()]);
	}

	protected function getDownload(): ?GDO_Download
	{
		return $this->getObject();
	}

	public function execute(): GDT
	{
		# File
		$dl = $this->getDownload();

		# Security
		$user = GDO_User::current();
		if (!$dl->canView($user))
		{
			throw new GDO_Error('err_gdo_not_found',
				[
					$dl->gdoHumanName(),
					$dl->getID(),
				]);
		}

		# Render
		return GDT_Response::makeWith(GDT_CardView::make()->gdo($dl));
	}

}
