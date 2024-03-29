<?php
namespace GDO\Download\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Response;
use GDO\Core\Method;
use GDO\Date\Time;
use GDO\Download\GDO_Download;
use GDO\Util\Common;

final class Accept extends Method
{

	public function isAlwaysTransactional(): bool { return true; }

	public function execute(): GDT
	{
		$table = GDO_Download::table();
		$id = Common::getRequestString('id', '0');
		if (
			(!($download = $table->find($id, false))) ||
			($download->gdoHashcode() !== Common::getRequestString('token'))
		)
		{
			return $this->error('err_gdo_not_found', [$table->gdoClassName(), get_called_class(), html($id)]);
		}
		$download->saveVars([
			'dl_accepted' => Time::getDate(),
			'dl_acceptor' => Common::getGetInt('by'),
		]);
		return GDT_Response::make();
	}

}
