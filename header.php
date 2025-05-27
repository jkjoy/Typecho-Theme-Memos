<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="referrer" content="no-referrer">
	<title><?php if($this->_currentPage>1) echo '第 '.$this->_currentPage.' 页 - '; ?><?php $this->archiveTitle(array(
            'category'  =>  _t('分类 %s 下的文章'),
            'search'    =>  _t('包含关键字 %s 的文章'),
            'tag'       =>  _t('标签 %s 下的文章'),
            'date'      =>  _t('在 %s 发布的文章'),
            'author'    =>  _t('%s 发布的文章')
        ), '', ' - '); ?>
        <?php $this->options->title(); ?><?php if ($this->is('index')) echo ' - '; ?>
        <?php if ($this->is('index')) $this->options->description() ?></title>
    <link rel="icon" href="<?php $this->options->icoUrl() ?>" type="image/*" />
	<link href="<?php $this->options->themeUrl('assets/css/style.css'); ?>" rel="stylesheet" type="text/css">
	<link href="<?php $this->options->themeUrl('assets/css/APlayer.min.css'); ?>" rel="stylesheet" type="text/css">
	<link href="<?php $this->options->themeUrl('assets/css/highlight.github.min.css'); ?>" rel="stylesheet" type="text/css">
    <?php $this->header("generator=&template=&pingback=&wlw=&xmlrpc=&rss1=&atom=&rss2=/feed"); ?>
    <?php $this->options->addhead(); ?>
</head>
<body>
	<header>
		<div class="menu">
			<div class="title">
                <?php //$this->options->title() ?>
            </div>
			<div class="pages">
                <a href="<?php $this->options->siteUrl(); ?>">主页</a>   
            <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
            <?php while($pages->next()): ?>
                <a <?php if($this->is('page', $pages->slug)): ?> class="current"<?php endif; ?> href="<?php $pages->permalink(); ?>" title="<?php $pages->title(); ?>"><?php $pages->title(); ?></a>
            <?php endwhile; ?>	
			</div>
		</div>		
		<div class='theme-toggle'>🌓</div>
	</header>