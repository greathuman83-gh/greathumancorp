<script src="https://unpkg.com/babel-standalone@6.26.0/babel.min.js"></script>
<script src="https://uicdn.toast.com/chart/latest/toastui-chart.min.js"></script>
<script class="code-js" id="code-js">
  const el = document.getElementById('chart-area');
  const data = {
	<?php if($d['c_code'] != '006'){//트리맵 이외의 차트?>
		categories: [
		<?php for($i=0;$i<count((array)$categoryArray);$i++){?>
		  '<?=$categoryArray[$i]?>',
		<?php }?>
		],
	<?php }?>

	<?php if($d['c_code'] == '002'){//컬럼라인차트?>
		series: {
			column: [
			<?php for($i=0;$i<count((array)$chartData['chart']);$i++){?>
			  {
				name: '<?=$chartData['chart'][$i]['chartTitle']?>',
				data: [<?=$chartData['chart'][$i]['chartData']?>],
				<?php if(count((array)$chartData['chart']) < 2){?>
					colorByCategories : true,
				<?php }?>
			  },
			<?php }?>
			],
			line: [
			{
				name: '<?=$d['title_line']?>',
				data: [<?=$d['line_content']?>],
				visible: true,
			},
			],
		},
	<?php }else if($d['c_code'] == '004'){//파이차트?>
		series: [
		<?php if(count((array)$chartData['chart']) > 0){?>
			<?php for($i=0;$i<count((array)$chartData['chart']);$i++){?>
			  {
				name: '<?=$chartData['chart'][$i]['chartTitle']?>',
				data: <?=$chartData['chart'][$i]['chartData']?>,
			  },
			<?php }?>
		<?php }?>
		],
	<?php }else if($d['c_code'] == '006'){//트리맵?>
	  series: [
		<?php if(count((array)$chartData['chart']) > 0){?>
			<?php for($i=0;$i<count((array)$chartData['chart']);$i++){?>
			 {
			  label: '<?=$chartData['chart'][$i]['chartTitle']?>',
			  data: <?=$chartData['chart'][$i]['chartData']?>,
			},
			<?php }?>
		<?php }?>
	  ],
	<?php }else{?>
		series: [
		<?php if(count((array)$chartData['chart']) > 0){?>
			<?php for($i=0;$i<count((array)$chartData['chart']);$i++){?>
			  {
				name: '<?=$chartData['chart'][$i]['chartTitle']?>',
				data: [<?=$chartData['chart'][$i]['chartData']?>],
				<?php if(count((array)$chartData['chart']) < 2 && ($d['c_code'] == '001' || $d['c_code'] == '002')){//컬럼&컬럼라인차트?>
					colorByCategories : true,
				<?php }?>
			  },
			<?php }?>
		<?php }?>
		],
	<?php }?>
  };

 const theme = {
	 //메인 타이틀
	/*
	 title:{
		  fontFamily: 'Noto Sans KR',
		  fontSize: 25,
		  fontWeight: 400,
		  color: '#555',
	 },
	*/
	//X축 타이틀 
	xAxis: {
	  title: {
		fontFamily: 'Noto Sans KR',
		fontSize: 13,
		//fontWeight: 400,
		//color: '#ff416d'
	  },
	  label: {
		fontFamily: 'Noto Sans KR',
		fontSize: 13,
		fontWeight: 600,
		color: '#555'
	  },
	  //width: 2,
	  //color: '#6655EE'
	},
	//Y축 타이틀 
	yAxis: {
		title: {
			fontFamily: 'Noto Sans KR',
			fontSize: 13,
			//fontWeight: 400,
			//color: '#03C03C'
		},
		label: {
			fontFamily: 'Noto Sans KR',
			fontSize: 13,
			fontWeight: 600,
			color: '#555'
		},
		//width: 3,
		//color: '#88ddEE'
	},


	//시리즈 옵션
	series: {
		colors: [
			<?php for($i=0;$i<count((array)$seriesColorArray);$i++){?>
				'<?=$seriesColorArray[$i]?>',
			<?php }?>
			<?php if($d['line_color']){?>
				'<?=$d['line_color']?>',
			<?php }?>
		],
		dataLabels: {
			fontSize: 13,
			fontFamily: 'Noto Sans KR',
			//fontWeight: 600,
			<?php if($d['label_color']){?>
				color: '<?=$d['label_color']?>',
			<?php }?>
			textBubble: {
				visible: false,
			},
			stackTotal: {
				fontSize: 15,
				/*
				fontWeight: 20,
				fontFamily: 'monaco',
				color: '#ffffff',
				textBubble: {
					visible: true,
					paddingY: 6,
					borderWidth: 3,
					borderColor: '#00bcd4',
					borderRadius: 7,
					backgroundColor: '#041367',
					shadowOffsetX: 0,
					shadowOffsetY: 0,
					shadowBlur: 0,
					shadowColor: 'rgba(0, 0, 0, 0)'
				}
				*/
			}
		},
		
	},
  };

  const options = {
	responsive: {//반응형 세팅
		animation: { duration: 300 },
		rules: [
			{
				condition: ({ width: w }) => {
				  return w <= 767;
				},
				options: {
					xAxis: {
						title: { 
							text: '',
						},
						tick: { interval: 4 },
						label: { 
							interval: 4,
							formatter: (value) => ``,
						}
					},
					yAxis: {
						title: { 
							text: '',
						},
						tick: { interval: 2 },
						label: {
							interval: 2,
							formatter: (value) => ``,
						}
					},
					legend: {
						align: 'top'
					},
				}
			},
		],
	},
	tooltip: {
		formatter: (value) => `${number_format(value)}`,
	},
	series: {
		<?php if($d['c_code'] == '005'){//컬럼스택차트?>
			stack: { type: 'normal' },
		<?php }?>

		selectable: true, //개별선택
		dataLabels: {//데이터 라벨
			<?php if($d['c_code'] == '006'){//트리맵만 허용?>
				visible: true,
			<?php }else{?>
				visible: false,
			<?php }?>
			formatter: (value) => `${number_format(value)}`,
			anchor: 'center',
			stackTotal: {
				visible: true,
				formatter: (value) => `${number_format(value)}`,
			},

			useTreemapLeaf: true,
			pieSeriesName: { visible: true }
		}
	},
	chart: {
		title: '',
		width: 'auto',
		height: 'auto',
	},

	//범례 박스
	legend: {
		color:'blue',
		visible:true,
		showCheckbox: false,
		align: 'right',
		
	},

	xAxis: { 
		pointOnColumn: true,
		title: { 
			text: '<?=$d['title_x']?>',
			//offsetY: 30,
		}
	},
	yAxis: {
			title: { 
				text: '<?=$d['title_y']?>',
				//offsetY: 50,
			},
			label: {
				formatter: (value) => `${number_format(value)}`,
			},
			<?php if($d['scale_max'] > 0 && $d['scale_stepsize'] > 0){?>
				scale: { // y축 범위 조정
					min: <?=$d['scale_min']?>, // 최하
					max: <?=$d['scale_max']?>, // 최상
					stepSize : <?=$d['scale_stepsize']?> // 간격
				},
			<?php }?>
	},
	//내보내기 메뉴
	exportMenu: {
		visible: false,
	},
	//테마 적용
	theme,
  };

//차트 호출
<?=$_chartCall[$d['c_code']]?>


/*================== 데이터 라벨 클릭 이벤트 ===================================*/
<?php if($d['page_code'] == '001' && !$menuCode){//KV AT GLANCE의 컬럼차트 && 프론트에서만 작동?>
	//셀렉트 이벤트
	/*
	//파이차트
	chart.on('selectSeries', (ev) => {
		const { label, value } = ev.pie[0].data;
		alert(`${label}: ${value}`);
	});
	*/
	chart.on('selectSeries', (ev) => {
		const { label, category, value,gtest } = ev.column[0].data;
		console.log(label)
		//해당 연도 트리맵 차트 불러오기
		$(top.document).find('#treemapChart iframe').attr('src','./chart.php?investType=<?=$investType?>&year='+category+'&label='+label);
		//alert(`(${label}, ${category}): ${value}`);
	});

	chart.on('unselectSeries', () => {
		//alert('unselect!');
	});
<?php }?>
/*================== 데이터 라벨 클릭 이벤트 끝 ==================================*/


// 숫자에 , 를 출력
function number_format(obj)
{
	let data = String(obj);
	let regx = new RegExp(/(-?\d+)(\d{3})/);
	let bExists = data.indexOf(".", 0);//0번째부터 .을 찾는다.
	let strArr = data.split('.');
	while (regx.test(strArr[0])) {//문자열에 정규식 특수문자가 포함되어 있는지 체크
		//정수 부분에만 콤마 달기 
		strArr[0] = strArr[0].replace(regx, "$1,$2");//콤마추가하기
	}
	if (bExists > -1) {
		//. 소수점 문자열이 발견되지 않을 경우 -1 반환
		data = strArr[0] + "." + strArr[1];
	} else { //정수만 있을경우 //소수점 문자열 존재하면 양수 반환 
		data = strArr[0];
	}
	return data;//문자열 반환
}
</script>