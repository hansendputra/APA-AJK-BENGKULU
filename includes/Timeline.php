<?php 
class Timeline
{
    public function render($idpeserta)
    {
        $klaim = mysql_fetch_array(mysql_query("
        SELECT ifnull(date_format(approve_time,'%d-%m-%Y'),'')as tglpengajuan, 
            ifnull(date_format(tgllengkapdokumen,'%d-%m-%Y'),'') as tgldoklengkap,    		
            ifnull(date_format(tglinfoasuransi,'%d-%m-%Y'),'')as tgllaporklaim,
            ifnull(date_format(tglbayarasuransi,'%d-%m-%Y'),'')as tglbayarasuransi,
            ifnull(date_format(tglbayar,'%d-%m-%Y'),'')as tglbayarclient  
        FROM ajkcreditnote
        WHERE del is null and idpeserta = '".$idpeserta."'"));
        $query = "SELECT * FROM ajktimeline";
        
        $result = mysql_query($query);
        $rs = array();
    
        $no = 0;
        while($row = mysql_fetch_array($result)){
            $no++;
            if($row['nmtimeline'] == "Pengajuan Klaim"){
                $date = $klaim['tglpengajuan'];
            }elseif($row['nmtimeline'] == "Dokumen Lengkap"){
                $date = $klaim['tgldoklengkap'];
            }elseif($row['nmtimeline'] == "Info Ke Asuransi"){
                $date = $klaim['tgllaporklaim'];
            }elseif($row['nmtimeline'] == "Dibayar dari Asuransi"){
                $date = $klaim['tglbayarasuransi'];
            }elseif($row['nmtimeline'] == "Dibayar Ke Bank"){
                $date = $klaim['tglbayarclient'];
            }elseif($row['nmtimeline'] == "Finish"){
                $date = $klaim['tglbayarclient'];
            }
            if($date == "00-00-0000"){
                $date = '';
            }
            $rs[] = array(
                'id'=>$row['id'],
                'status'=>$row['nmtimeline'],
                'date'=>$date,
                'desc'=>$date.':'.$row['nmtimeline']
            );
        }

        $numDots= count($rs);	
        $parentWidthBase= 0.7;
        $parentWidth= $parentWidthBase * $numDots * 10;
        $parentMaxWidth= '1000px';
        $dotWidth= '25px';
        $dotWidthSm= '17px';
        $active= '#2C3E50'; 
        $inactive= '#AEB6BF';

        echo "
        <style>	  
            h1 {
                text-align: center;
                height: 38px;
                margin: 60px 0;
            }
            h1 span {
                white-space: nowrap;
            }
            
            .flex-parent {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                width: 100%;
                height: 100%;
            }
            
            .input-flex-container {
                display: flex;
                justify-content: space-around;
                align-items: center;
                width: ".$parentWidth."vw;
                height: 100px;
                max-width: ".$parentMaxWidth.";
                position: relative;
                z-index: 0;
            }
            
            .input {
                width: 25px;
                height: 25px;
                background-color: #2C3E50;
                position: relative;
                border-radius: 50%;
            }
            .input:hover {
                cursor: pointer;
            }
            .input::before, .input::after {
                content: '';
                display: block;
                position: absolute;
                z-index: -1;
                top: 50%;
                transform: translateY(-50%);
                background-color: #2C3E50;
                width: ".($parentWidth / $numDots)."vw;
                height: 5px;
                max-width: 50px;
            }
            .input::before {
                left: calc(-4vw + 12.5px);
            }
            .input::after {
                right: calc(-4vw + 12.5px);
            }
            .input.actives {
                background-color: #2C3E50;
            }
            .input.actives::before {
                background-color: #2C3E50;
            }
            .input.actives::after {
                background-color: #AEB6BF;
            }
            .input.actives span {
                font-weight: 700;
            }
            .input.actives span::before {
                font-size: 13px;
            }
            .input.actives span::after {
                font-size: 14px;
            }
            .input.actives ~ .input, .input.actives ~ .input::before, .input.actives ~ .input::after {
                background-color: #AEB6BF;
            }
            .input span {
                width: 1px;
                height: 1px;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                visibility: hidden;
                text-align: center;
            }
            .input span::before, .input span::after {
                visibility: visible;
                position: absolute;
                left: 50%;
            }
            .input span::after {
                content: attr(data-year);
                top: 25px;
                transform: translateX(-50%);
                font-size: 12px;
                width: 100px;
            }
            .input span::before {
                content: attr(data-info);
                top: -65px;
                width: 70px;
                transform: translateX(-5px) rotateZ(-45deg);
                font-size: 12px;
                text-indent: -10px;
            }
            
            .description-flex-container {
                // width: 80vw;
                font-weight: 400;
                font-size: 16px;
                margin-top: 20px;
                max-width: 1000px;
            }
            .description-flex-container p {
                margin-top: 0;
                display: none;
            }
            .description-flex-container p.actives {
                display: block;
            }
            
            @media (min-width: 1250px) {
            .input::before {
                left: -37.5px;
            }
            
            .input::after {
                right: -37.5px;
            }
            }
            @media (max-width: 850px) {
            .input {
                width: 17px;
                height: 17px;
            }
            .input::before, .input::after {
                height: 3px;
            }
            .input::before {
                left: calc(-4vw + 8.5px);
            }
            .input::after {
                right: calc(-4vw + 8.5px);
            }
            }
            @media (max-width: 600px) {
            .flex-parent {
                justify-content: initial;
            }
            
            .input-flex-container {
                flex-wrap: wrap;
                justify-content: center;
                width: 100%;
                height: auto;
                margin-top: 15vh;
            }
            
            .input {
                width: 60px;
                height: 60px;
                margin: 0 10px 50px;
                background-color: #AEB6BF;
            }
            .input::before, .input::after {
                content: none;
            }
            .input span {
                width: 100%;
                height: 100%;
                display: block;
            }
            .input span::before {
                top: calc(100% + 5px);
                transform: translateX(-50%);
                text-indent: 0;
                text-align: center;
            }
            .input span::after {
                top: 50%;
                transform: translate(-50%, -50%);
                color: #ECF0F1;
            }
            
            .description-flex-container {
                margin-top: 30px;
                text-align: center;
            }
            }
            @media (max-width: 400px) {
            body {
                min-height: 950px;
            }
            }

            *, *:before, *:after {
            box-sizing: border-box;
        }
        </style>";

        echo '<br />
			<div class="flex-parent">
				<div class="input-flex-container">';
				$a = 0;
				$flag = [];
          foreach ($rs as $c) {
              $a++;
              if ($c['date']!='') {
                      $flag[] = $c;
              }
				}
				
				$b = 0;
				foreach ($rs as $r) {
					$b++;
					$status = '';
					if(count($flag)==$b){
						$status = 'actives';
					}
                    echo '<div id="time_'.$r['id'].'" class="input '.$status.'">
						<span data-year="'.$r['date'].'" data-info="'.$r['status'].'"></span>
					</div>';
                }
					
				echo '</div>
				<div class="description-flex-container">';
					$c = 0;
					foreach ($rs as $r) {
						$c++;
						$status = '';
						if(count($flag)==$c){
							$status = 'active';
						}
						// echo '<p id="desc_'.$r['id'].'" class="'.$status.'">'.$r['desc'].'</p>';
					}
					
				echo '</div>
			</div>';

			echo "<script>
			$(function(){
				var inputs = $('.input');
				var paras = $('.description-flex-container').find('p');
				$(inputs).click(function(){
					var t = $(this),
							ind = t.index(),
							matchedPara = $(paras).eq(ind);
					
					$(t).add(matchedPara).addClass('active');
					$(inputs).not(t).add($(paras).not(matchedPara)).removeClass('active');
				});
			});

			
			$(window).resize(function(){
				console.log($(window).width(),$('.input-flex-container').width(),(( window.outerWidth - 10 ) / window.innerWidth)*100)
				if ($('.input-flex-container').width() > 537){
					var size = 34;
					$('.input-flex-container').css('width', size+'vw');
				}else{
					$('.input-flex-container').css('width', '42vw');
				}
				
			});
			</script>";
    }
}

?>