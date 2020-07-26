extends CanvasLayer

var opened = false
var can_close = true

signal on_open
signal on_close

func _show(_message, _can_close = true):
	can_close = _can_close
	$Node2D/HBoxContainer/Control/close.self_modulate.a = int(_can_close)
	
	$Node2D/CenterContainer/message.text = str(_message)
	$anim.play("show")
	yield($anim,"animation_finished")
	opened = true
	emit_signal("on_open")

func _on_close_pressed():
	$anim.play_backwards("show")
	yield($anim, "animation_finished")
	opened = false
	Loader.close()
	emit_signal("on_close")
	
func _input(event):
	if !opened: return
	if !can_close: return
	
	if event is InputEventScreenTouch or event is InputEventMouseButton:
		if event.is_pressed():
			_on_close_pressed()
			return
		
	if event is InputEventKey:
		if event.scancode in [KEY_ESCAPE, KEY_ENTER, KEY_KP_ENTER]:
			_on_close_pressed()
			
