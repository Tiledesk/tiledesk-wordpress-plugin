<script type="application/javascript">
    window.tiledeskSettings = <?php echo wp_json_encode( $widget_config ); ?>;
    (function(e,t,n){var c=window;var e=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(e){i.q.push(e)};c.Tiledesk=i;var r,s=e.getElementsByTagName(t)[0];if(e.getElementById(n))return;r=e.createElement(t);r.id=n;r.async=true;r.src="<?php echo esc_url( $tiledesk_jssdk_url ); ?>";s.parentNode.insertBefore(r,s)})(document,"script","tiledesk-jssdk");
</script>
