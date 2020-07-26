extends CanvasLayer

var prog:HTTPRequest = null

func _ready():
	$anim.current_animation = "start"
	$anim.stop(true);

func open():
	if $anim.current_animation_position == 0:
		$anim.play("start")
	
func close():
	if $anim.current_animation_position > 0:
		$anim.play_backwards("start")

func _process(_delta):
	if weakref(prog).get_ref():
	
		var bodySize = prog.get_body_size()
		var downloadedBytes = prog.get_downloaded_bytes()
		
		var percent = int(downloadedBytes * 100 / bodySize)
		percent = clamp(percent, 0 , 100)
		
		var progressBar = $CenterContainer/Control/progress
		if percent > 0 && percent < 100:
			progressBar.show()
			progressBar.value = percent
		else:
			progressBar.value = 0
			progressBar.hide()
			prog = null
		
