<style>
    a
    {
        padding: 7px;
        background: #ddd;
        margin-left: 10px;
        text-decoration: none;
    }
</style>

<?php
require_once "baglanti.php";
require_once "voting.php";
$baglanti = new baglanti();
$voting = new voting();


$id = intval($_GET['id']);
$cek = $baglanti->db->prepare("SELECT * FROM movzular WHERE id = :id");
$cek->bindParam(":id",$id,PDO::PARAM_INT);
$cek->execute();
$melumatgeldi = $cek->fetch(PDO::FETCH_ASSOC);


// Oylama işləmi

if(isset($_GET['voting'])) //Əgər istifadəçi səs veribsə
{
    $voting1 = intval($_GET['voting']);
    $voting->process($id, $voting1); //voting.php Classındakı process metodunu işlət

    header('Location: ?id='.$id);

}
else
{
    echo "Lütfən Düşüncənizi bildirin";
    echo '<hr>';
}
// Səs vermə işləmi

    echo 'Mövzu adı: ' . $melumatgeldi['isim'];
    echo '<br/>';
    echo 'Mövzu daxili: ' . $melumatgeldi['yazi'];
    echo '<br/>';
    echo '<br/>';


    foreach ($voting->$votinglist as $key => $value) //$key burada səsvermədə olan seçimlərin dəyişkənidi (əla, yaxşı,orta,pis,çox pis)
    {
        $sayi = $voting->goster($id,$key);

        echo '<a href="?id='.$id.'&voting='.$key.'">'.$value.' ('.$sayi.')</a>';
    }

