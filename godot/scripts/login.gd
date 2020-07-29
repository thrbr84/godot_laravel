extends Node2D

var on_focus = null
var inputMode = "login"

func _ready() -> void:
	Loader.close()
	
	Message.connect("on_open", self, "_on_message_open")
	Message.connect("on_close", self, "_on_message_close")
	HttpLayer.connect("http_completed", self, "http_completed")
	
	btnLoginAction("login")

func _setFocus() -> void:
	if on_focus == null:
		for i in get_tree().get_nodes_in_group("inputs"):
			if i.is_visible_in_tree():
				i.grab_focus()
				i._on_input_focus_entered()
				break

func _clear() -> void:
	for f in get_tree().get_nodes_in_group("inputs"):
		f.text = ""
	$CanvasLayer/loader.hide()
	
func _on_linkForgotPass_pressed() -> void:
	_clear()
	if inputMode != "login":
		btnLoginAction("login")
	else:
		btnLoginAction("forgot_password")

func _on_linkNewUser_pressed() -> void:
	_clear()
	if inputMode != "register":
		btnLoginAction("register")
	else:
		btnLoginAction("login")

func _on_message_open() -> void:
	$CanvasLayer/Login.hide()
	$CanvasLayer/loader.hide()

func _on_message_close() -> void:
	$CanvasLayer/Login.show()
	$CanvasLayer/loader.hide()
	
func _input(event) -> void:
	if event is InputEventKey:
		if event.is_pressed():
			if event.scancode in [KEY_ENTER, KEY_KP_ENTER]:
				_on_btnConfirm_pressed()
			if event.scancode in [KEY_ESCAPE]:
				_on_btnCancel_pressed()

func btnLoginAction(_act) -> void:
	inputMode = _act
	
	for f in get_tree().get_nodes_in_group("inputs"):
		f.hide()
	
	$CanvasLayer/Login/VBoxContainer/ControlLinks.hide()
	$CanvasLayer/Login/VBoxContainer/ControlButtons/HBoxContainer/btnConfirm.hide()
	$CanvasLayer/Login/VBoxContainer/ControlButtons/HBoxContainer/btnCancel.hide()
	match _act:
		"register":
			showFields(
				"New User",
				["inputName", "inputLastname", "inputCodename", "inputEmail", "inputPassword", "inputPassword2"]
			)
			$CanvasLayer/Login/VBoxContainer/ControlButtons/HBoxContainer/btnConfirm.show()
			$CanvasLayer/Login/VBoxContainer/ControlButtons/HBoxContainer/btnCancel.show()
		
		"login":
			showFields(
				"Login",
				["inputEmail", "inputPassword"]
			)
			$CanvasLayer/Login/VBoxContainer/ControlLinks.show()
			$CanvasLayer/Login/VBoxContainer/ControlLinks/HBoxContainer/linkForgotPassword/Label.text = "Forgot password"
			$CanvasLayer/Login/VBoxContainer/ControlLinks/HBoxContainer/linkNewUser/Label.text = "New user"
			$CanvasLayer/Login/VBoxContainer/ControlButtons/HBoxContainer/btnConfirm.show()
		
		"forgot_password":
			showFields(
				"Create new password!",
				["inputEmail"]
			)
			$CanvasLayer/Login/VBoxContainer/ControlButtons/HBoxContainer/btnConfirm.show()
			$CanvasLayer/Login/VBoxContainer/ControlButtons/HBoxContainer/btnCancel.show()
		
		"reset_password":
			showFields(
				"New password!", 
				["inputPasswordCode", "inputPassword", "inputPassword2"]
			)
			$CanvasLayer/Login/VBoxContainer/ControlButtons/HBoxContainer/btnConfirm.show()
			$CanvasLayer/Login/VBoxContainer/ControlButtons/HBoxContainer/btnCancel.show()
	
	_setFocus()

func _on_btnCancel_pressed() -> void:
	_clear()
	match inputMode:
		"register": btnLoginAction("login")
		"forgot_password","reset_password": btnLoginAction("login")

func _on_btnConfirm_pressed() -> void:
	if Message.opened: return
	
	match inputMode:
		"login":
			HttpLayer._login({
				"email": $CanvasLayer/Login/VBoxContainer/inputEmail.text,
				"password": $CanvasLayer/Login/VBoxContainer/inputPassword.text
			}, "res://scenes/main.tscn")
		
		"register":
			HttpLayer._register({
				"name": $CanvasLayer/Login/VBoxContainer/inputName.text,
				"lastname": $CanvasLayer/Login/VBoxContainer/inputLastname.text,
				"codename": $CanvasLayer/Login/VBoxContainer/inputCodename.text,
				"email": $CanvasLayer/Login/VBoxContainer/inputEmail.text,
				"password": $CanvasLayer/Login/VBoxContainer/inputPassword.text,
				"c_password": $CanvasLayer/Login/VBoxContainer/inputPassword2.text
			}, "res://scenes/main.tscn")
		
		"forgot_password":
			HttpLayer._forgotPassword({
				"email": $CanvasLayer/Login/VBoxContainer/inputEmail.text
			})
			btnLoginAction("reset_password")
		
		"reset_password":
			HttpLayer._resetPassword({
				"code": $CanvasLayer/Login/VBoxContainer/inputPasswordCode.text,
				"password": $CanvasLayer/Login/VBoxContainer/inputPassword.text,
				"c_password": $CanvasLayer/Login/VBoxContainer/inputPassword2.text
			})

	$CanvasLayer/loader.show()

func http_completed(res, response_code, headers, route) -> void:
	if res == null : return
	if res.status == "success" && route == "reset_password":
		btnLoginAction("login")

func showFields(_title="",_fields=[]) -> void:
	$CanvasLayer/Login/Label.text = _title
	for f in _fields:
		$CanvasLayer/Login/VBoxContainer.get_node(f).show()
