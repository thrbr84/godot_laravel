extends LineEdit

func _on_input_focus_entered():
	$AnimationPlayer.play("line")

func _on_input_focus_exited():
	$AnimationPlayer.play_backwards("line")
