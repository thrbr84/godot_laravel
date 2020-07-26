extends Node2D

func _ready():
	# check autenticated
	var _ret = HttpLayer._redirectIfLogged("res://scenes/main.tscn", "res://scenes/login.tscn")
