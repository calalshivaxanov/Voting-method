<?php


class voting extends baglanti
{                        //0      1       2      3      4
  public $votinglist = ["Əla","Yaxşı","Orta","Pis","Çox Pis"];

    //Eyni IP dən olan istifadəçinin səsvermə edib etmədiyini yoxlamaq

  public function kontrol($movzu_id)
  {
        $ip = $_SERVER['REMOTE_ADDR']; //IP ni alırıq localhostumuzun

        $control = $this->db->prepare("SELECT * FROM sesvermeler WHERE ip = :ip and konu_id = :konuid"); //DB`də ip və konu_id`si eyni olan məlumatın olun olmamasını yoxlayırıq
        $control->bindParam(":ip",$ip,PDO::PARAM_STR);
        $control->bindParam(":movzuid",$movzu_id,PDO::PARAM_INT);
        $control->execute();
        $c = $control->rowCount(); //Sayını tap

        return $c; //Varsa sayını döndər
      }



    //DB- işləmi səsvermə,səsdəyişmə,səssilmə
      public function process($movzu_id,$voting_id)
      {
        $ip = $_SERVER['REMOTE_ADDR']; //İstifadəçinin İPsini alırıq

       $c = $this->kontrol($movzu_id); //Yuxarıda tanımladığımız $c dəyişkənini buraya çəkirik


       if($c == 0) //DB`də bu barədə heçbir verilən yoxdursa
       {
           //məlumat daxil olacaq
         $elave_et = $this->db->prepare("INSERT INTO sesvermeler(ip,movzu_id,voting_id)VALUES(?,?,?)");
           $elave_et->execute(array($ip,$movzu_id,$voting_id));
           //Həmin id`ni daxil elə
       }
       else
       {
           // 1.voting id si eynidirmi? deye yoxla
         $kontrol = $this->db->prepare("SELECT * FROM sesvermeler WHERE ip = :ip and movzu_id = :movzuid");
         $kontrol->bindParam(":ip",$ip,PDO::PARAM_STR);
         $kontrol->bindParam(":movzuid",$movzu_id,PDO::PARAM_INT);
         $kontrol->execute();
         $cek = $kontrol->fetch(PDO::FETCH_ASSOC);

           if($movzu_id != $cek['movzu_id']) //Əgər mənim səs verdiyim sütunla(id) DB`də ki id eyni deyilsə
           {
             $update = $this->db->prepare("UPDATE sesvermeler SET voting_id = ? WHERE id = ?");
             $update->execute(array($movzu_id,$cek['id']));
               //Həmən id`ni yenilə
           }
           else //Yox əgər eynidirsə
           {
             $delete = $this->db->prepare("DELETE FROM sesvermeler WHERE id = ?");
             $delete->execute(array($cek['id']));
               //Həmin id`ni sil...
           }

         }
       }



    //Hansı səs sütununa nə qədər səs verildiyini yoxlamaq üçün metod
       public function goster($movzu_id,$voting_id)
       {
        $sorgu = $this->db->prepare("SELECT * FROM sesvermeler WHERE movzu_id = :movzuuid and voting_id = :votingid");
        $sorgu->bindParam(":movzuid",$movzu_id,PDO::PARAM_INT);
        $sorgu->bindParam(":votingid",$voting_id,PDO::PARAM_INT);
        $sorgu->execute();
        return $sorgu->rowCount();
      }







    }