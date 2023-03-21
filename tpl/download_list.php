<?php
namespace GDO\Download\tpl;

/** @var GDO_Download $download * */

/**
 * This is the default download list item template.
 * It has no html at all, so it should be compatible with all themes :)
 */

use GDO\Download\GDO_Download;
use GDO\Table\GDT_ListItem;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Button;
use GDO\UI\GDT_Container;
use GDO\UI\GDT_Label;
use GDO\User\GDO_User;
use GDO\Votes\GDT_VoteSelection;

# ListItem
$li = GDT_ListItem::make()->gdo($download);

$li->creatorHeader(null, null, $download->displayTitle());

# Content
$content = GDT_Container::make()->vertical();
$content->addField($download->gdoColumn('dl_info'));
if ($download->isPaid())
{
	$content->addField($download->gdoColumn('dl_price'));
}
$li->content($content);

# Subtext
$subc = GDT_Bar::make()->horizontal()->css('width', 'fit-content');
$subc->addField(GDT_VoteSelection::make()->gdo($download));
$subc->addField($download->gdoColumn('dl_rating'));
$subc->addField($download->gdoColumn('dl_votes'));
$subc->addField($download->gdoColumn('dl_downloads'));
$subc->addField(GDT_Label::make('downloads'));
$content->addField($subc);

# Actions
$li->actions()->addFields(
	GDT_Button::make('btn_download')->href($download->href_view())->icon('download'),
	GDT_Button::make('btn_edit')->href($download->href_edit())->icon('edit')->disabled(!$download->canEdit(GDO_User::current())),
);

# Render
echo $li->render();
