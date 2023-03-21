<?php
namespace GDO\Download\Method;

use GDO\Admin\MethodAdmin;
use GDO\Core\GDO;
use GDO\Download\GDO_Download;
use GDO\Table\MethodQueryTable;

/**
 * Staff overview of downloads.
 *
 * @version 6.10
 * @since 6.04
 * @author gizmore
 */
final class Admin extends MethodQueryTable
{

	use MethodAdmin;

	public function getPermission(): ?string { return 'staff'; }

	public function gdoTable(): GDO { return GDO_Download::table(); }

}
