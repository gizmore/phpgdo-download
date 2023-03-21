<?php
namespace GDO\Download;

use GDO\Votes\GDO_VoteTable;

/**
 * Vote table for downloads.
 *
 * @version 6.10.1
 * @since 6.0.0
 * @author gizmore
 */
final class GDO_DownloadVote extends GDO_VoteTable
{

	public function gdoVoteObjectTable() { return GDO_Download::table(); }

	public function gdoVoteGuests() { return Module_Download::instance()->cfgGuestVotes(); }

}
