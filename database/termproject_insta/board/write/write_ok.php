<?php
    include "../../db_info.php";
?>
<?php
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
?>

<?php
    function image_upload($message_id){
        /*** check if a file was uploaded ***/
        if(is_uploaded_file($_FILES['image']['tmp_name'])){
            if(getimagesize($_FILES['image']['tmp_name']) != false)
            {
                /***  get the image info. ***/
                    $size = getimagesize($_FILES['image']['tmp_name']);
                /*** assign our variables ***/
                $type = $size['mime'];
                $imgfp = fopen($_FILES['image']['tmp_name'], 'rb');
                $size = $size[3];
                $name = $_FILES['image']['name'];
                $maxsize = 99999999;
                
    
                /***  check the file is less than the maximum file size ***/
                    if($_FILES['image']['size'] < $maxsize )
                {
                    /*** connect to db ***/
                    $dbh = new PDO("mysql:host=localhost;dbname=jarvis", 'jarvis', 'database#6');
                    
                    /*** set the error mode ***/
                    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    /*** our sql query ***/
                    $stmt = $dbh->prepare("INSERT INTO `image` (image_type ,`image`, image_size, image_name, message_id) VALUES (? ,?, ?, ?, ?)");
                    
                    /*** bind the params ***/
                    $stmt->bindParam(1, $type);
                    $stmt->bindParam(2, $imgfp, PDO::PARAM_LOB);
                    $stmt->bindParam(3, $size);
                    $stmt->bindParam(4, $name);
                    $stmt->bindParam(5, $message_id);
                    
                    /*** execute the query ***/
                    $stmt->execute();
                }
                else
                {
                    /*** throw an exception is image is not of type ***/
                    throw new Exception("File Size Error");
                }
            } else {
            // if the file is not less than the maximum allowed, print an error
            throw new Exception("Unsupported Image Format!");
            }
        }
    }
?>

<?php
    $ID = $_POST['id'];
    $content = $_POST['content'];
    $timestamp = date("Y-m-d H:i:s",time());

    
    if($ID && $content){
        $result=sq("INSERT INTO `message`(writer_id, content, message_time) values('".$ID."','".$content."', '".$timestamp."')");
        $last_insert_id=$db->insert_id;
        image_upload($last_insert_id);
        echo "<script>
                alert('글쓰기 완료되었습니다.');
                location.href='../board.php';
            </script>";
    }else{
        echo "<script>
                alert('글쓰기에 실패했습니다.');
                history.back();
            </script>";
    }

    
?>

