<?php
//koneksi ke database, username,password  dan namadatabase menyesuaikan 
mysql_connect('localhost', 'root');
mysql_select_db('datasekolah');
 
//memanggil file excel_reader
require "excel_reader.php";


if(isset($_POST['submit'])){
 
    $target = basename($_FILES['filedata_sdall']['name']) ;
    move_uploaded_file($_FILES['filedata_sdall']['tmp_name'], $target);
 
// tambahkan baris berikut untuk mencegah error is not readable
    chmod($_FILES['filedata_sdall']['name'],0777);
    
    $data = new Spreadsheet_Excel_Reader($_FILES['filedata_sdall']['name'],false);
    
//    menghitung jumlah baris file xls
    $baris = $data->rowcount($sheet_index=0);
    
//    jika kosongkan data dicentang jalankan kode berikut
    $drop = isset( $_POST["drop"] ) ? $_POST["drop"] : 0 ;
    if($drop == 1){
//             kosongkan tabel data_sd
             $truncate ="TRUNCATE TABLE data_sd";
             mysql_query($truncate);
    };
    
//    import data excel mulai baris ke-2 (karena tabel xls ada header pada baris 1)
    for ($i=3; $i<=$baris; $i++)
    {
//       membaca data (kolom ke-1 sd terakhir)
      $NPSN            = $data->val($i, 1,0);
      $NAMA_SP        = $data->val($i, 2,0);
      $DESA           = $data->val($i, 3,0);
      $STATUS         = $data->val($i, 4,0);
      $AKREDITASI     = $data->val($i, 5,0);
 
 
 
//      setelah data dibaca, masukkan ke tabel data_sd sql
      $query = "INSERT into data_sd (NPSN,NAMA_SP,DESA,STATUS,AKREDITASI)values('$NPSN','$NAMA_SP','$DESA','$STATUS','$AKREDITASI')";
      $hasil = mysql_query($query);
    }

     $baris = $data->rowcount($sheet_index=1);
    
    for ($i=2; $i<=$baris; $i++)
    {
//       membaca data (kolom ke-1 sd terakhir)
      $nip            = $data->val($i, 1,1);
      $nama           = $data->val($i, 2,1);
      $b_studi        = $data->val($i, 3,1);
      
 
 
 
//      setelah data dibaca, masukkan ke tabel data_sd sql
      $query = "INSERT into data_sd (nip,nama,b_studi)values('$nip','$nama','$b_studi')";
      $hasil = mysql_query($query);
    }

    if(!$hasil){
//          jika import gagal
          die(mysql_error());
      }else{
//          jika impor berhasil
          echo "Data berhasil diimpor.";
    }
    
//    hapus file xls yang udah dibaca
    unlink($_FILES['filedata_sdall']['name']);
}
 
 
?>
 
<form name="myForm" id="myForm" onSubmit="return validateForm()" action="import.php" method="post" enctype="multipart/form-data">
    <input type="file" id="filedata_sdall" name="filedata_sdall" />
    <input type="submit" name="submit" value="Import" /><br/>
    <label><input type="checkbox" name="drop" value="1" /> <u>Kosongkan tabel sql terlebih dahulu.</u> </label>
</form>
 
<script type="text/javascript">
//    validasi form (hanya file .xls yang diijinkan)
    function validateForm()
    {
        function hasExtension(inputID, exts) {
            var fileName = document.getElementById(inputID).value;
            return (new RegExp('(' + exts.join('|').replace(/\./g, '\\.') + ')$')).test(fileName);
        }
 
        if(!hasExtension('filedata_sdall', ['.xls'])){
            alert("Hanya file XLS (Excel 2003) yang diijinkan.");
            return false;
        }
    }
</script>