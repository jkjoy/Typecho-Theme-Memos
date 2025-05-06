<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<footer class="markdown-body footer">
		<p>Copyright @ <?php echo date('Y'); ?>  <?php $this->options->title(); ?>  All Rights Reserved.<a href="<?php $this->options->sitemapurl() ?>" target="_blank" class="hidden" aria-label="网站地图">💗</a>
        <?php //添加加载时间控制
            if ($this->options->showtime): ?>
            &nbsp;页面加载耗时<?php echo timer_stop();?> 
            <?php endif; ?>  <?php $this->options->tongji(); ?> </p>
</footer>
<div id="img-lightbox"><img src="" alt="原图"></div>
	<script type="text/javascript" src="<?php $this->options->themeUrl('assets/js/APlayer.min.js'); ?>"></script>
	<script type="text/javascript" src="<?php $this->options->themeUrl('assets/js/Meting.min.js'); ?>"></script>
	<script type="text/javascript" src="<?php $this->options->themeUrl('assets/js/main.js'); ?>"></script>
</body>
</html>