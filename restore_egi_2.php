
$egi = \App\Models\Egi::find(2);
if ($egi) {
    $egi->owner_id = $egi->user_id;
    $egi->mint = false;
    $egi->co_creator_id = null;
    $egi->save();
    echo "[[RESTORED_SUCCESSFULLY]]";
} else {
    echo "[[EGI_NOT_FOUND]]";
}
exit();
