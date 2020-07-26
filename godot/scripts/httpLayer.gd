extends CanvasLayer

var endpoint_api = "http://godotlaravel.local/api/"

var use_threads = false

signal http_completed(res, response_code, headers, route)

func _on_HTTPRequest_request_completed(result, response_code, headers, body, route, httpObject, redirectTo = null):
	var json = JSON.parse(body.get_string_from_utf8())
	var res = json.result
	
	emit_signal("http_completed", res, response_code, headers, route)

	if res == null:
		res = {}
		
	if res.has("status"):
		
		match res.status:
			"error":
				var msg = res.message
				
				if TYPE_DICTIONARY == typeof(msg):
					msg = ""
					for k in res.message.keys():
						msg = str(msg, "\n - ", res.message[k][0])
				
				Message._show(msg)
				_destroyHttpObject(httpObject)
				return
			
			"success":
				match route:
					"logout":
						var _changeScene = get_tree().change_scene_to(load("res://scenes/login.tscn"))
						_destroyHttpObject(httpObject)
						return
					
					"remove_account":
						var _changeScene = get_tree().change_scene_to(load("res://scenes/login.tscn"))
						_destroyHttpObject(httpObject)
						return
					
					"save_data":
						Game.saving = false
						Game.modified = false
						Loader.close()
						Alert._show("Save successful!", "onSaveSucessfull", null, null, { "btnConfirm" : "OK" })
						_destroyHttpObject(httpObject)
						return
					
					"forgot_password":
						Message._show(res.message)
						_destroyHttpObject(httpObject)
						return
					
					"reset_password":
						Message._show(res.message)
						_destroyHttpObject(httpObject)
						return
					
					"login", "register":
						Game.user_token = res['data']['token']
						Game.user_data = res['data']['user']
						Game.save_data = Game.user_data['save_data']
				
	if redirectTo != null && redirectTo != "no":
		var _changeScene = get_tree().change_scene_to(load(redirectTo))
		
	Loader.prog = null
	Loader.close()
	
	# remove http request
	_destroyHttpObject(httpObject)

func _destroyHttpObject(_object):
	if weakref(_object).get_ref():
		_object.queue_free()

func _apiCore(_endpoint, _data, _authorize = false, _method="GET", _route = "", _redirectTo = null):
	var http = HTTPRequest.new()
	http.use_threads = use_threads
	http.connect("request_completed", self, "_on_HTTPRequest_request_completed", [_route, http, _redirectTo])
	add_child(http)
	
	var headers = [
		"Content-Type: application/json",
		"Accept: application/json",
		str("Language: ", Game.language),
		]
	
	if _authorize:
		headers.append(str("Authorization: Bearer ", Game.user_token))
		
	Loader.prog = http
	
	var http_error = http.request(str(endpoint_api, _endpoint), headers, false, HTTPClient[str("METHOD_",_method)], to_json(_data))
	if http_error != OK:
		Loader.prog = null
		Loader.close()

func _login(_credentials, _redirectTo):
	_apiCore("login", _credentials, false, "POST", "login", _redirectTo)
	
func _register(_credentials, _redirectTo):
	_apiCore("register", _credentials, false, "POST", "register", _redirectTo)
	
func _forgotPassword(_forgotData):
	_apiCore("forgot_password", _forgotData, false, "POST", "forgot_password")

func _resetPassword(_resetData, _redirectTo = null):
	_apiCore("reset_password", _resetData, false, "PUT", "reset_password", _redirectTo)

func _logoff(simple = false):
	if !simple:
		_apiCore("user/logout", null, true, "GET", "logout")
	
	# reset variables
	Game.user_token = null
	Game.user_data = null
	Game.save_data = null

func _saveGame():
	var game_info = {
		"save_data": Game.save_data
	}
	_apiCore("user", game_info, true, "PUT", "save_data")

func _removeAccount():
	_apiCore("user", null, true, "DELETE", "remove_account")

	# reset variables
	Game.user_token = null
	Game.user_data = null
	Game.save_data = null

func _redirectIfLogged(redirectSucess=null, redirectError=null):
	var auth = Game.user_token
	if redirectSucess != null && auth != null:
		var _changeScene = get_tree().change_scene_to(load(redirectSucess))
	if redirectError != null && auth == null:
		var _changeScene = get_tree().change_scene_to(load(redirectError))
	
	return auth
