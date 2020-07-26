extends Control

var local_level = 1
var local_total_points = 0

func _ready():
	_loadSave()
	$ControlActions/btnIncreaseLevel/touch.connect("pressed", self, "_on_touchIncrease_pressed", ["local_level"])
	$ControlActions/btnIncreasePoints/touch.connect("pressed", self, "_on_touchIncrease_pressed", ["local_total_points"])
	$ControlActions/btnResetGame/touch.connect("pressed", self, "_on_touchIncrease_pressed", ["reset_game"])
	$ControlActions/btnDeleteAccount/touch.connect("pressed", self, "_on_touchIncrease_pressed", ["delete_account"])

func _loadSave():
	local_level = int(Game.save_data["level"])
	local_total_points = int(Game.save_data["total_points"])
	$HBoxContainer/Level/Label.text = str("Level: ", local_level)
	$HBoxContainer/Points/Label.text = str("Pts: ", local_total_points)

func _on_btnLogoff_pressed():
	if Message.opened: return
	if Alert.opened: return
	
	Alert._show("Do you really want to log out?", "onLogout", [self,"_on_logoff"], null, { "btnConfirm" : "YES", "btnCancel": "No" })

func _on_saveGame_pressed():
	if Message.opened: return
	if Alert.opened: return
	
	HttpLayer._saveGame()

func _on_touchIncrease_pressed(_target):
	if Message.opened: return
	if Alert.opened: return
	
	if _target == "delete_account":
		Alert._show("Do you really want to delete your account?", "onRemoveAccount", [self,"_on_delete_account"], null, { "btnConfirm" : "YES", "btnCancel": "No" })

		
	elif _target == "reset_game":
		Game.save_data = {
			"level": 1,
			"total_points": 0
		}
	else:
		self[_target] = self[_target] + 1
		Game.save_data = {
			"level": local_level,
			"total_points": local_total_points
		}
	_loadSave()

func _on_logoff():
	HttpLayer._logoff()
	
func _on_delete_account():
	HttpLayer._removeAccount()
