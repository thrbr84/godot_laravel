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
		
		if res.status == "error":
			var msg = res.message
			
			if TYPE_DICTIONARY == typeof(msg):
				msg = ""
				for k in res.message.keys():
					msg = str(msg, "\n - ", res.message[k][0])
			
			Message._show(msg)
			return
		
		if res.status == "success" && route == "logout":
			var _changeScene = get_tree().change_scene_to(load("res://scenes/login.tscn"))
			return
			
		if res.status == "success" && route == "remove_account":
			var _changeScene = get_tree().change_scene_to(load("res://scenes/login.tscn"))
			return

		if res.status == "success" && route == "save_data":
			Game.saving = false
			Game.modified = false
			Loader.close()
			Alert._show("Save sucessfull!", "onSaveSucessfull", null, null, { "btnConfirm" : "OK" })
			return
			
		if res.status == "success" && route == "forgot_password":
			Message._show(res.message)
			return
		
		if res.status == "success" && route == "reset_password":
			Message._show(res.message)
			return
		
		if res.status == "success" && route == "login":
			# get token
			Game.user_token = res['data']['token']
			Game.user_data = res['data']['user']
			Game.save_data = Game.user_data['save_data']

		if res.status == "success" && route == "register":
			# get token
			Game.user_token = res['data']['token']
			Game.user_data = res['data']['user']
			Game.save_data = Game.user_data['save_data']
			
	if redirectTo != null && redirectTo != "no":
		var _changeScene = get_tree().change_scene_to(load(redirectTo))
		
	Loader.prog = null
	Loader.close()
	
	# remove http request
	if weakref(httpObject).get_ref():
		httpObject.queue_free()

func _login(_credentials, _redirectTo):
	var http = HTTPRequest.new()
	http.use_threads = use_threads
	http.connect("request_completed", self, "_on_HTTPRequest_request_completed", ["login", http, _redirectTo])
	add_child(http)
	
	var headers = [
		"Content-Type: application/json",
		"Accept: application/json",
		str("Language: ", Game.language),
		]
	Loader.prog = http
	
	var http_error = http.request(str(endpoint_api, 'login'), headers, false, HTTPClient.METHOD_POST, to_json(_credentials))
	if http_error != OK:
		Loader.prog = null
		Loader.close()

func _register(_credentials, _redirectTo):
	var http = HTTPRequest.new()
	http.use_threads = use_threads
	http.connect("request_completed", self, "_on_HTTPRequest_request_completed", ["register", http, _redirectTo])
	add_child(http)
	
	var headers = [
		"Content-Type: application/json",
		"Accept: application/json",
		str("Language: ", Game.language),
		]
	Loader.prog = http
	var http_error = http.request(str(endpoint_api, 'register'), headers, false, HTTPClient.METHOD_POST, to_json(_credentials))
	if http_error != OK:
		Loader.prog = null
		Loader.close()
		
func _forgotPassword(_forgotData):
	var http = HTTPRequest.new()
	http.use_threads = use_threads
	http.connect("request_completed", self, "_on_HTTPRequest_request_completed", ["forgot_password", http])
	add_child(http)
	
	var headers = [
		"Content-Type: application/json",
		"Accept: application/json",
		str("Language: ", Game.language),
		]
	Loader.prog = http
	var http_error = http.request(str(endpoint_api, 'forgot_password'), headers, false, HTTPClient.METHOD_POST, to_json(_forgotData))
	if http_error != OK:
		Loader.prog = null
		Loader.close()


func _resetPassword(_resetData, _redirectTo = null):
	var http = HTTPRequest.new()
	http.use_threads = use_threads
	http.connect("request_completed", self, "_on_HTTPRequest_request_completed", ["reset_password", http, _redirectTo])
	add_child(http)

	var headers = [
		"Content-Type: application/json",
		"Accept: application/json",
		str("Language: ", Game.language),
		]
	Loader.prog = http
	var http_error = http.request(str(endpoint_api, 'reset_password'), headers, false, HTTPClient.METHOD_PUT, to_json(_resetData))
	if http_error != OK:
		Loader.prog = null
		Loader.close()


func _me(_redirectTo = null):
	var http = HTTPRequest.new()
	http.use_threads = use_threads
	http.connect("request_completed", self, "_on_HTTPRequest_request_completed", ["me", http, _redirectTo])
	add_child(http)

	var headers = [
		"Content-Type: application/json",
		"Accept: application/json",
		str("Language: ", Game.language),
		str("Authorization: Bearer ", Game.user_token)
		]
	Loader.prog = http
	var http_error = http.request(str(endpoint_api, 'user'), headers, false, HTTPClient.METHOD_GET)
	if http_error != OK:
		Loader.prog = null
		Loader.close()


func _logoff(simple = false):
	if !simple:
		var http = HTTPRequest.new()
		http.use_threads = use_threads
		http.connect("request_completed", self, "_on_HTTPRequest_request_completed", ["logout", http])
		add_child(http)
		
		var headers = [
			"Content-Type: application/json",
			"Accept: application/json",
			str("Language: ", Game.language),
			str("Authorization: Bearer ", Game.user_token)
			]
		Loader.prog = http
		var http_error = http.request(str(endpoint_api, 'user/logout'), headers, false, HTTPClient.METHOD_GET)
		if http_error != OK:
			Loader.prog = null
			Loader.close()
	
	# reset variables
	Game.user_token = null
	Game.user_data = null
	Game.save_data = null

func _saveGame():
	var http = HTTPRequest.new()
	http.use_threads = use_threads
	http.connect("request_completed", self, "_on_HTTPRequest_request_completed", ["save_data", http])
	add_child(http)
	
	var headers = [
		"Content-Type: application/json",
		"Accept: application/json",
		str("Language: ", Game.language),
		str("Authorization: Bearer ", Game.user_token)
		]
	
	var game_info = {
		"save_data": Game.save_data
	}
	
	Loader.prog = http
	var http_error = http.request(str(endpoint_api, 'user'), headers, false, HTTPClient.METHOD_PUT, to_json(game_info))
	if http_error != OK:
		Loader.prog = null
		Loader.close()
		

func _removeAccount():
	var http = HTTPRequest.new()
	http.use_threads = use_threads
	http.connect("request_completed", self, "_on_HTTPRequest_request_completed", ["remove_account", http])
	add_child(http)
	
	var headers = [
		"Content-Type: application/json",
		"Accept: application/json",
		str("Language: ", Game.language),
		str("Authorization: Bearer ", Game.user_token)
		]
		
	Loader.prog = http
	var http_error = http.request(str(endpoint_api, 'user'), headers, false, HTTPClient.METHOD_DELETE, to_json({}))
	if http_error != OK:
		Loader.prog = null
		Loader.close()
		
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
