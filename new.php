<?php
// -------------------- PHP DATA --------------------
$robots = [
    ['name'=>'Robo-1','energy'=>85,'health'=>92,'task'=>'Exploration'],
    ['name'=>'Robo-2','energy'=>60,'health'=>80,'task'=>'Maintenance'],
    ['name'=>'Robo-3','energy'=>40,'health'=>65,'task'=>'Transport'],
];

$tasks = [
    ['robot'=>'Robo-1','task'=>'Scan area','status'=>'In-Progress'],
    ['robot'=>'Robo-2','task'=>'Repair motor','status'=>'Done'],
    ['robot'=>'Robo-3','task'=>'Deliver package','status'=>'Pending'],
];

$sensors = [
    ['type'=>'Temperature','value'=>'36°C'],
    ['type'=>'Proximity','value'=>'12m'],
    ['type'=>'Voltage','value'=>'24V'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Futuristic Robotics Dashboard</title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* -------------------- GENERAL STYLES -------------------- */
* { margin:0; padding:0; box-sizing:border-box; font-family: 'Orbitron', sans-serif;}
body { background:#0a0f1a; color:#00fff0; overflow-x:hidden; }

/* -------------------- HEADER -------------------- */
header {
  width:100%; padding:20px; text-align:center;
  font-size:2rem; font-weight:700;
  background:linear-gradient(90deg, #00fff0, #8a2be2);
  color:#fff; letter-spacing:2px;
  box-shadow:0 0 20px rgba(0,255,240,0.5);
}

/* -------------------- SECTIONS -------------------- */
section { padding:50px 10%; scroll-snap-align:start; }

/* -------------------- SCROLL SNAP -------------------- */
html { scroll-behavior:smooth; scroll-snap-type: y mandatory; }

/* -------------------- STATUS RINGS -------------------- */
.rings-container { display:flex; justify-content:space-around; flex-wrap:wrap; margin-top:50px; }
.ring-card {
  position:relative; width:200px; height:200px; margin:20px;
  border-radius:50%; background:rgba(0,255,240,0.05);
  box-shadow:0 0 20px rgba(0,255,240,0.3);
}
.ring-card canvas { position:absolute; top:0; left:0; }

/* -------------------- CARD TEXT -------------------- */
.ring-card .info {
  position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
  text-align:center; color:#00fff0;
}
.ring-card .info h3 { margin-bottom:5px; font-size:1.2rem; }
.ring-card .info p { font-size:1rem; }

/* -------------------- FLEET CARDS -------------------- */
.fleet-container { display:flex; justify-content:space-around; flex-wrap:wrap; margin-top:50px; }
.fleet-card {
  background:rgba(138,43,226,0.1); padding:20px; margin:10px;
  border-radius:15px; width:220px; text-align:center;
  box-shadow:0 0 15px rgba(138,43,226,0.5); transition:0.3s;
}
.fleet-card:hover { transform:scale(1.05); box-shadow:0 0 30px rgba(138,43,226,0.7); }
.fleet-card h4 { margin-bottom:10px; font-size:1.2rem; color:#fff; }
.fleet-card p { margin:5px 0; color:#00fff0; }

</style>
</head>
<body>

<!-- -------------------- HEADER -------------------- -->
<header>Futuristic Robotics Dashboard</header>

<!-- -------------------- ROBOT STATUS RINGS -------------------- -->
<section id="status-rings">
  <h2 style="text-align:center; margin-bottom:40px;">Robot Status Overview</h2>
  <div class="rings-container">
    <?php foreach($robots as $robot): ?>
    <div class="ring-card">
      <canvas id="ring-<?php echo $robot['name']; ?>"></canvas>
      <div class="info">
        <h3><?php echo $robot['name']; ?></h3>
        <p>Energy: <?php echo $robot['energy']; ?>%</p>
        <p>Health: <?php echo $robot['health']; ?>%</p>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<script>
// -------------------- RING ANIMATIONS --------------------
<?php foreach($robots as $robot): ?>
var ctx<?php echo $robot['name']; ?> = document.getElementById('ring-<?php echo $robot['name']; ?>').getContext('2d');
var energy<?php echo $robot['name']; ?> = <?php echo $robot['energy']; ?>;
var health<?php echo $robot['name']; ?>;

new Chart(ctx<?php echo $robot['name']; ?>, {
    type: 'doughnut',
    data: {
        labels: ['Energy','Missing'],
        datasets: [{
            data: [energy<?php echo $robot['name']; ?>, 100-energy<?php echo $robot['name']; ?>],
            backgroundColor: ['#00fff0','#0a0f1a'],
            borderWidth:2,
            hoverOffset:4
        }]
    },
    options: {
        cutout:'70%',
        responsive:true,
        plugins:{ legend:{ display:false } }
    }
});
<?php endforeach; ?>
</script>
<!-- -------------------- FLEET OVERVIEW -------------------- -->
<section id="fleet-overview">
  <h2 style="text-align:center; margin-bottom:40px;">Fleet Overview</h2>
  <div class="fleet-container">
    <?php foreach($robots as $robot): ?>
      <div class="fleet-card">
        <h4><?php echo $robot['name']; ?></h4>
        <p>Task: <?php echo $robot['task']; ?></p>
        <p>Energy: <?php echo $robot['energy']; ?>%</p>
        <p>Health: <?php echo $robot['health']; ?>%</p>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- -------------------- TASK QUEUE / LOGS -------------------- -->
<section id="task-logs">
  <h2 style="text-align:center; margin-bottom:40px;">Task Queue & Logs</h2>
  <div style="width:90%; margin:0 auto; overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse; text-align:center;">
      <thead>
        <tr style="background:rgba(0,255,240,0.2);">
          <th style="padding:10px;">Robot</th>
          <th style="padding:10px;">Task</th>
          <th style="padding:10px;">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($tasks as $task): 
            $color = ($task['status']=='Done')?'#00ff7f':(($task['status']=='In-Progress')?'#ffff00':'#ff4d4d');
        ?>
          <tr style="border-bottom:1px solid rgba(0,255,240,0.1); color:<?php echo $color; ?>;">
            <td style="padding:10px;"><?php echo $task['robot']; ?></td>
            <td style="padding:10px;"><?php echo $task['task']; ?></td>
            <td style="padding:10px;"><?php echo $task['status']; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<!-- -------------------- SENSORS OVERVIEW -------------------- -->
<section id="sensors-overview">
  <h2 style="text-align:center; margin-bottom:40px;">Sensors Overview</h2>
  <div class="fleet-container">
    <?php foreach($sensors as $sensor): ?>
      <div class="fleet-card">
        <h4><?php echo $sensor['type']; ?></h4>
        <p>Value: <?php echo $sensor['value']; ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<style>
/* -------------------- TASK LOG STYLES -------------------- */
#task-logs table th, #task-logs table td {
  border-radius:5px;
  padding:10px;
  font-weight:500;
  font-size:0.95rem;
}
#task-logs table th { color:#fff; letter-spacing:1px; }
#task-logs table tbody tr:hover { background:rgba(0,255,240,0.1); }

/* -------------------- SENSOR CARDS -------------------- */
#sensors-overview .fleet-card {
  width:180px; height:100px; padding:15px; 
  background:rgba(0,255,240,0.05);
  box-shadow:0 0 15px rgba(0,255,240,0.3);
  border-radius:10px; display:flex; flex-direction:column;
  justify-content:center; align-items:center;
}
#sensors-overview .fleet-card h4 { margin-bottom:5px; color:#fff; font-size:1rem; }
#sensors-overview .fleet-card p { color:#00fff0; font-size:0.9rem; }

/* -------------------- SECTION HEADERS -------------------- */
section h2 {
  font-size:2rem; color:#fff; text-shadow:0 0 10px #00fff0;
}
</style>
<!-- -------------------- CHARTS SECTION -------------------- -->
<section id="charts">
  <h2 style="text-align:center; margin-bottom:40px;">Fleet Analytics</h2>
  <div style="display:flex; flex-wrap:wrap; justify-content:space-around; gap:50px;">
    
    <!-- BATTERY CHART -->
    <div style="width:300px; height:300px; background:rgba(0,255,240,0.05); border-radius:20px; box-shadow:0 0 20px rgba(0,255,240,0.3); padding:20px;">
      <h4 style="text-align:center; color:#fff; margin-bottom:10px;">Battery Levels</h4>
      <canvas id="batteryChart"></canvas>
    </div>
    
    <!-- TASKS CHART -->
    <div style="width:300px; height:300px; background:rgba(138,43,226,0.05); border-radius:20px; box-shadow:0 0 20px rgba(138,43,226,0.3); padding:20px;">
      <h4 style="text-align:center; color:#fff; margin-bottom:10px;">Tasks Completed</h4>
      <canvas id="tasksChart"></canvas>
    </div>
    
    <!-- SENSOR CHART -->
    <div style="width:300px; height:300px; background:rgba(255,20,147,0.05); border-radius:20px; box-shadow:0 0 20px rgba(255,20,147,0.3); padding:20px;">
      <h4 style="text-align:center; color:#fff; margin-bottom:10px;">Sensors Overview</h4>
      <canvas id="sensorsChart"></canvas>
    </div>
    
  </div>
</section>

<!-- -------------------- FOOTER -------------------- -->
<footer style="text-align:center; padding:30px; color:#00fff0; letter-spacing:1px; text-shadow:0 0 10px #00fff0;">
  Futuristic Robotics Dashboard &copy; 2025 | All Rights Reserved
</footer>

<script>
// -------------------- CHART DATA FROM PHP --------------------
var robotNames = <?php echo json_encode(array_column($robots,'name')); ?>;
var robotEnergy = <?php echo json_encode(array_column($robots,'energy')); ?>;
var robotHealth = <?php echo json_encode(array_column($robots,'health')); ?>;

// Battery Chart
var ctxBattery = document.getElementById('batteryChart').getContext('2d');
var batteryChart = new Chart(ctxBattery, {
    type: 'bar',
    data: {
        labels: robotNames,
        datasets: [{
            label: 'Energy %',
            data: robotEnergy,
            backgroundColor: 'rgba(0,255,240,0.6)',
            borderColor: 'rgba(0,255,240,1)',
            borderWidth: 2,
            hoverBackgroundColor: 'rgba(0,255,240,0.8)'
        }]
    },
    options: {
        responsive:true,
        plugins:{ legend:{ display:false } },
        scales:{
            y:{ beginAtZero:true, max:100, ticks:{ color:'#00fff0' } },
            x:{ ticks:{ color:'#00fff0' } }
        }
    }
});

// Tasks Completed Chart (Simulated)
var taskStatus = <?php 
    $done = 0; $inProgress = 0; $pending = 0;
    foreach($tasks as $t){
        if($t['status']=='Done') $done++;
        elseif($t['status']=='In-Progress') $inProgress++;
        else $pending++;
    }
    echo json_encode([$done,$inProgress,$pending]);
?>;
var ctxTasks = document.getElementById('tasksChart').getContext('2d');
var tasksChart = new Chart(ctxTasks, {
    type: 'doughnut',
    data:{
        labels:['Done','In-Progress','Pending'],
        datasets:[{
            data: taskStatus,
            backgroundColor:['#00ff7f','#ffff00','#ff4d4d'],
            hoverOffset:6,
            borderWidth:2
        }]
    },
    options:{
        cutout:'70%',
        plugins:{ legend:{ labels:{ color:'#00fff0' } } }
    }
});

// Sensors Chart (Simulated numeric values)
var sensorValues = <?php 
    $values = [];
    foreach($sensors as $s){
        preg_match('/\d+/', $s['value'],$m);
        $values[] = isset($m[0])?(int)$m[0]:0;
    }
    echo json_encode($values);
?>;
var sensorLabels = <?php echo json_encode(array_column($sensors,'type')); ?>;
var ctxSensors = document.getElementById('sensorsChart').getContext('2d');
var sensorsChart = new Chart(ctxSensors, {
    type:'line',
    data:{
        labels:sensorLabels,
        datasets:[{
            label:'Sensor Value',
            data:sensorValues,
            borderColor:'#ff1493',
            backgroundColor:'rgba(255,20,147,0.2)',
            tension:0.4,
            fill:true,
            pointBackgroundColor:'#ff1493'
        }]
    },
    options:{
        responsive:true,
        plugins:{ legend:{ labels:{ color:'#00fff0' } } },
        scales:{
            y:{ ticks:{ color:'#00fff0' } },
            x:{ ticks:{ color:'#00fff0' } }
        }
    }
});
</script>

<style>
/* -------------------- CHART HEADERS -------------------- */
#charts h4 { text-align:center; font-size:1.2rem; text-shadow:0 0 5px #00fff0; }

/* -------------------- FOOTER STYLING -------------------- */
footer { background:rgba(0,0,0,0.1); margin-top:50px; }
</style>
</body>
</html>
<!-- -------------------- PARTICLE BACKGROUND -------------------- -->
<canvas id="particleCanvas" style="position:fixed; top:0; left:0; width:100%; height:100%; z-index:-1;"></canvas>

<script>
// -------------------- PARTICLE BACKGROUND SCRIPT --------------------
const canvas = document.getElementById('particleCanvas');
const ctx = canvas.getContext('2d');
let particlesArray = [];
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

window.addEventListener('resize', () => {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
});

class Particle {
    constructor(){
        this.x = Math.random() * canvas.width;
        this.y = Math.random() * canvas.height;
        this.size = Math.random() * 2 + 1;
        this.speedX = Math.random() * 0.5 - 0.25;
        this.speedY = Math.random() * 0.5 - 0.25;
        this.color = 'rgba(0,255,240,0.7)';
    }
    update(){
        this.x += this.speedX;
        this.y += this.speedY;

        if(this.x > canvas.width) this.x = 0;
        if(this.x < 0) this.x = canvas.width;
        if(this.y > canvas.height) this.y = 0;
        if(this.y < 0) this.y = canvas.height;
    }
    draw(){
        ctx.fillStyle = this.color;
        ctx.beginPath();
        ctx.arc(this.x,this.y,this.size,0,Math.PI*2);
        ctx.fill();
    }
}

function initParticles(){
    particlesArray = [];
    for(let i=0; i<150; i++){
        particlesArray.push(new Particle());
    }
}

function animateParticles(){
    ctx.clearRect(0,0,canvas.width,canvas.height);
    particlesArray.forEach(p => { p.update(); p.draw(); });
    requestAnimationFrame(animateParticles);
}

initParticles();
animateParticles();
</script>

<style>
/* -------------------- HOVER GLOW EFFECTS -------------------- */
.fleet-card:hover, .ring-card:hover {
    box-shadow: 0 0 40px #00fff0, 0 0 60px #8a2be2, 0 0 80px #ff1493;
    transform: scale(1.08);
    transition:0.4s;
}

/* -------------------- RING CANVAS SMOOTH ANIMATION -------------------- */
.ring-card canvas {
    transition: all 1s ease-out;
}
</style>

<script>
// -------------------- SMOOTH RING ANIMATION --------------------
<?php foreach($robots as $robot): ?>
let ring<?php echo $robot['name']; ?> = document.getElementById('ring-<?php echo $robot['name']; ?>');
let currentValue<?php echo $robot['name']; ?> = 0;
let targetValue<?php echo $robot['name']; ?> = <?php echo $robot['energy']; ?>;

function animateRing<?php echo $robot['name']; ?>(){
    if(currentValue<?php echo $robot['name']; ?> < targetValue<?php echo $robot['name']; ?>){
        currentValue<?php echo $robot['name']; ?> += 1;
        ring<?php echo $robot['name']; ?>.chart.data.datasets[0].data[0] = currentValue<?php echo $robot['name']; ?>;
        ring<?php echo $robot['name']; ?>.chart.data.datasets[0].data[1] = 100 - currentValue<?php echo $robot['name']; ?>;
        ring<?php echo $robot['name']; ?>.chart.update();
        requestAnimationFrame(animateRing<?php echo $robot['name']; ?>);
    }
}
animateRing<?php echo $robot['name']; ?>();
<?php endforeach; ?>
</script>
