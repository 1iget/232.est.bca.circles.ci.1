<script src="<?php echo base_url(); ?>js/vendor/video.js"></script>

<script type="text/javascript">
    	videojs.options.flash.swf = "swf/video-js.swf";
</script>

<div class="popup" id="popup_video">	
	<div id="popup_video_holder">
		<video id="embeded_video" class="video-js vjs-bca-skin" controls preload="auto" 
		width="100%" height="100%" poster="img/popups/test_video_cover_001.jpg" data-setup="{}">
		    <source src="http://video-js.zencoder.com/oceans-clip.mp4" type='video/mp4' />
		    <source src="http://video-js.zencoder.com/oceans-clip.webm" type='video/webm' />
		    <source src="http://video-js.zencoder.com/oceans-clip.ogv" type='video/ogg' />
		  </video>
	</div>
	<div id="popup_video_footer">
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec sed sodales est. Vivamus non dolor tempus, aliquet diam sagittis, molestie tortor.</p>
		<ul id="popup_footer_share">
			<li>SHARE</li>
			<li class="share-ico" id="popup_footer_share_facebook" onclick="$popup.share({type:'facebook', url:''})"></li>
			<li class="share-ico" id="popup_footer_share_twitter" onclick="$popup.share({type:'twitter', url:''})"></li>
		</ul>
	</div>
</div>

<script type="text/javascript">
    	//FF Bug fix. hide control when video loaded.
		videojs("embeded_video").ready(function(){
			var myPlayer = this;
			var t =setTimeout(function(){
				clearTimeout(t);
				myPlayer.trigger('mouseout');
			},1000);
		});
</script>