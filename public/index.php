<?php
/*$currentpage = __FILE__;
require_once('../private/Main.php'); //MUST be imported.
import('Page.Page');

$page = Page::getPage();
$page->startPage();
$template = $page->getTemplate();

//Begin Page Processing
$page->setTitle(null);

$page->echoData('<div class="row">');

$page->echoData('<div class="col-sm-12">');
$page->echoData('<h1>News</h1>');
{
    $pg_num = 1;
    if (isset($_GET['page']) && is_numeric($_GET['page']))
        $pg_num = intval($_GET['page']);
    if ($pg_num < 1)
        $pg_num = 1;
    $pg_num = min(domsPlaceAddon\News::getTotalPages(), $pg_num);

    $posts = domsPlaceAddon\News::getFromPage($pg_num);
    $component = $template->getComponent('NewsSmall');
    foreach ($posts as $post) {
        $page->echoData($component->generate($template, $page, $post));
    }

    $page->echoData('<nav>');
    $page->echoData('<ul class="pager">');
    $page->echoData('<li class="previous ' . ($pg_num < 2 ? "disabled" : "") . '"><a href="' . File::getTopDirectoryAsHTTP() . '/?page=' . ($pg_num - 1) . '"><span aria-hidden="true">' . $template->generateIcon('chevron-left') . '</span> Newer</a></li>');
    $page->echoData('<li class="next ' . ($pg_num >= domsPlaceAddon\News::getTotalPages() ? "disabled" : "") . '"><a href="' . File::getTopDirectoryAsHTTP() . '/?page=' . ($pg_num + 1) . '">Older <span aria-hidden="true">' . $template->generateIcon('chevron-right') . '</span></a></li>');
    $page->echoData('</ul>');
    $page->echoData('</nav>');
}
$page->echoData('</div>');

$page->echoData('</div>');

//End Page Processing
$page->makePage();
$page->endPage();*/
