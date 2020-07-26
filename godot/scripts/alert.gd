extends CanvasLayer

var opened = false
var can_close = false

var nameAlert = ""
var ifTrue = null
var ifFalse = null

signal on_alert_action(_name, _confirm)
signal on_open
signal on_close

func _show(_message, _name, _ifTrue = null, _ifFalse = null, _labels = { "btnCancel": "Cancelar", "btnConfirm": "Confirmar" }):

	if _labels != null:
		var nodeButtons = $Node2D/CenterContainer/VBoxContainer/HBoxContainer
		for n in nodeButtons.get_children():
			n.hide()
		
		for k in _labels.keys():
			var nd = nodeButtons.get_node(str(k))
			if weakref(nd).get_ref():
				nd.get_node("label").text = str(_labels[k])
				nd.show()
	
	nameAlert = _name
	ifTrue = _ifTrue
	ifFalse = _ifFalse
	
	$Node2D/CenterContainer/VBoxContainer/message.text = str(_message)
	$anim.play("show")
	yield($anim,"animation_finished")
	opened = true
	emit_signal("on_open")

func _on_close_pressed():
	opened = false
	emit_signal("on_close")
	
func _input(event):
	if !opened: return
	
	if event is InputEventKey:
		if event.scancode == KEY_ESCAPE:
			_on_touchCancel_pressed()
			return
		elif event.scancode in [KEY_ENTER, KEY_KP_ENTER]:
			_on_touchConfirm_pressed()
			return
	
	if !can_close: return
	if event is InputEventScreenTouch or event is InputEventMouseButton:
		if event.is_pressed():
			_on_close_pressed()
			return

func _on_touchCancel_pressed():
	$anim.play_backwards("show")
	yield($anim, "animation_finished")
	emit_signal("on_alert_action", nameAlert, false)
	_on_close_pressed()

	if ifFalse != null:
		ifFalse[0].call(ifFalse[1])
	
func _on_touchConfirm_pressed():
	$anim.play_backwards("show")
	yield($anim, "animation_finished")
	emit_signal("on_alert_action", nameAlert, true)
	_on_close_pressed()

	if ifTrue != null:
		ifTrue[0].call(ifTrue[1])
