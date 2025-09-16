<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Robotics Engineer Dashboard</title>
<style>
  :root{
    --bg-1:#031018; --bg-2:#000;
    --neon:#00e6ff; --neon-2:#60f0ff; --muted:rgba(255,255,255,0.06);
    --panel-bg: rgba(3,26,36,0.45);
    --glass: rgba(255,255,255,0.03);
    --accent: #7ef0a8;
    font-family: Inter, "Segoe UI", Roboto, Arial, sans-serif;
  }
  html,body{height:100%;margin:0;background:radial-gradient(800px 400px at 50% 20%, rgba(2,20,30,0.6), rgba(0,0,0,1));color:#cfefff;overflow:hidden}
  .dashboard{display:grid;grid-template-columns: 320px 1fr 340px;grid-template-rows: 1fr 170px;gap:18px;height:100vh;padding:22px;box-sizing:border-box}
  /* LEFT column: analytics */
  .panel{background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));border:1px solid rgba(255,255,255,0.04);backdrop-filter: blur(6px);border-radius:12px;padding:12px;box-shadow:0 12px 40px rgba(0,0,0,0.7)}
  .left-col{grid-column:1/2;grid-row:1/2;display:flex;flex-direction:column;gap:12px}
  .chart-card{height:240px;display:flex;flex-direction:column;gap:8px}
  .chart-title{font-size:13px;font-weight:700;margin:0 0 2px 0}
  .mini-cards{display:flex;gap:8px}
  .mini{flex:1;padding:8px;border-radius:8px;background:linear-gradient(180deg,rgba(255,255,255,0.015),transparent);border:1px solid rgba(255,255,255,0.03);font-size:13px}
  .stat-val{font-size:18px;font-weight:800;margin-top:6px;color:var(--neon)}
  /* CENTER column: main core + small cores */
  .center-col{grid-column:2/3;grid-row:1/2;position:relative;display:flex;align-items:center;justify-content:center}
  /* main core */
  .main-core{position:relative;width:420px;height:420px;border-radius:12px;display:flex;align-items:center;justify-content:center}
  .core-svg{position:absolute;inset:0;pointer-events:none}
  .core-center{position:relative;z-index:6;text-align:center}
  .core-center h1{margin:0;font-size:20px;letter-spacing:1px}
  .core-center .big{font-size:28px;font-weight:800;color:var(--neon)}
  .core-aura{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);width:640px;height:640px;border-radius:50%;background:radial-gradient(circle, rgba(0,230,255,0.065), transparent 40%);filter:blur(50px);z-index:1;pointer-events:none}
  /* small cores (satellites) */
  .sat{position:absolute;width:180px;height:180px;display:flex;align-items:center;justify-content:center;border-radius:12px;pointer-events:none}
  .sat .svg{position:absolute;inset:0}
  .sat .label{position:relative;z-index:4;text-align:center;pointer-events:none}
  /* RIGHT column: feed */
  .right-col{grid-column:3/4;grid-row:1/2;display:flex;flex-direction:column;gap:12px}
  .feed-list{height:100%;overflow:hidden;display:flex;flex-direction:column;gap:8px}
  .feed-entry{padding:8px;border-radius:8px;background:linear-gradient(180deg, rgba(255,255,255,0.015), transparent);border:1px solid rgba(255,255,255,0.03);font-size:13px}
  /* BOTTOM row: console */
  .console{grid-column:1/4;grid-row:2/3;padding:12px;border-radius:10px;display:flex;flex-direction:column;gap:8px;overflow:hidden}
  .console .log{font-family:monospace;font-size:13px;line-height:1.3;color:#b7ffe9}
  /* chart canvas sizing */
  canvas{background:transparent;display:block}
  /* small UI niceties */
  .label-small{font-size:12px;opacity:0.8}
  .donut-legend{display:flex;gap:8px;flex-wrap:wrap}
  .dot{width:10px;height:10px;border-radius:2px;margin-right:6px;display:inline-block}
  /* responsive */
  @media (max-width:1200px){ .dashboard{grid-template-columns: 240px 1fr;grid-template-rows:1fr 180px 0; grid-auto-rows:0 } .right-col{display:none} }
</style>
</head>
<body>
<div class="dashboard">

  <!-- LEFT: Analytics -->
  <div class="left-col">
    <div class="panel chart-card" id="barCard">
      <div class="chart-title">Actuator Torque & AI Load</div>
      <canvas id="barChart" width="280" height="150"></canvas>
      <div class="label-small">Torque (Nm) vs Neural Activity (%) — live</div>
    </div>

    <div class="panel" style="display:flex;gap:12px;align-items:stretch">
      <div style="flex:1">
        <div class="chart-title">Energy Distribution</div>
        <canvas id="donutChart" width="140" height="140"></canvas>
        <div class="donut-legend" id="donutLegend"></div>
      </div>
      <div style="flex:1;display:flex;flex-direction:column;gap:8px">
        <div class="mini">
          <div class="label-small">Core Temp</div>
          <div class="stat-val" id="statTemp">— °C</div>
        </div>
        <div class="mini">
          <div class="label-small">Signal Integrity</div>
          <div class="stat-val" id="statIntegrity">— %</div>
        </div>
        <div class="mini">
          <div class="label-small">System Efficiency</div>
          <div class="stat-val" id="statEfficiency">— %</div>
        </div>
      </div>
    </div>
  </div>

  <!-- CENTER: Main core + satellites -->
  <div class="center-col">
    <div class="core-aura"></div>

    <!-- MAIN CORE: bigger, complex rings -->
    <div class="main-core" id="mainCore">
      <svg class="core-svg" viewBox="0 0 200 200" preserveAspectRatio="xMidYMid meet">
        <!-- 4 rings with different styles -->
        <g transform="translate(100,100)">
          <circle cx="0" cy="0" r="88" stroke="rgba(0,230,255,0.16)" stroke-width="6" fill="none" stroke-dasharray="24 12" class="ringA"></circle>
          <circle cx="0" cy="0" r="68" stroke="rgba(80,250,255,0.12)" stroke-width="4" fill="none" stroke-dasharray="6 18" class="ringB"></circle>
          <circle cx="0" cy="0" r="48" stroke="rgba(160,255,255,0.14)" stroke-width="3" fill="none" stroke-dasharray="3 8" class="ringC"></circle>
          <circle cx="0" cy="0" r="28" stroke="rgba(0,230,255,0.88)" stroke-width="2.8" fill="none" class="ringD"></circle>
        </g>
      </svg>
      <div class="core-center" style="z-index:6;text-align:center">
        <h1 style="margin:0;font-size:14px;opacity:0.85">MAIN CORE</h1>
        <div class="big" id="mainCoreLoad">— %</div>
        <div style="font-size:12px;opacity:0.8" id="mainCoreSub">stability · sync</div>
      </div>
    </div>

    <!-- 4 small cores, evenly spaced on a half-circle arc above the main core -->
    <!-- positions hardcoded for perfect symmetry -->
    <div class="sat" id="sat1" style="left:calc(50% - 360px); top:8%">
      <svg class="svg" viewBox="0 0 200 200">
        <g transform="translate(100,100)">
          <!-- <circle r="70" class="ring1" stroke="rgba(0,220,255,0.10)" stroke-width="6" fill="none" stroke-dasharray="20 10"></circle>
          <circle r="52" class="ring2" stroke="rgba(80,240,255,0.12)" stroke-width="3" fill="none" stroke-dasharray="8 16"></circle>
          <circle r="34" class="ring3" stroke="rgba(120,255,255,0.14)" stroke-width="2" fill="none" stroke-dasharray="3 10"></circle>
          <circle r="18" class="ring4" stroke="rgba(0,230,255,0.95)" stroke-width="2" fill="none"></circle> -->
        </g>
      </svg>
      <div class="label">
        <!-- <div style="font-weight:700">AI CORE</div> -->
        <div style="font-size:12px;opacity:0.9" id="sat1sub"></div>
      </div>
    </div>

    <div class="sat" id="sat2" style="left:calc(50% - 160px); top:3%">
      <svg class="svg" viewBox="0 0 200 200">
        <g transform="translate(100,100)">
          <!-- <circle r="70" class="ring1" stroke="rgba(0,200,255,0.10)" stroke-width="6" fill="none" stroke-dasharray="24 8"></circle>
          <circle r="52" class="ring2" stroke="rgba(60,230,255,0.12)" stroke-width="3" fill="none" stroke-dasharray="6 14"></circle>
          <circle r="34" class="ring3" stroke="rgba(110,240,255,0.14)" stroke-width="2" fill="none" stroke-dasharray="4 8"></circle>
          <circle r="18" class="ring4" stroke="rgba(0,240,255,0.95)" stroke-width="2" fill="none"></circle> -->
        </g>
      </svg>
      <div class="label">
        <!-- <div style="font-weight:700">SENSOR MATRIX</div> -->
        <div style="font-size:12px;opacity:0.9" id="sat2sub"></div>
      </div>
    </div>

    <div class="sat" id="sat3" style="left:calc(50% + 160px); top:3%">
      <svg class="svg" viewBox="0 0 200 200">
        <g transform="translate(100,100)">
          <!-- <circle r="70" class="ring1" stroke="rgba(0,210,255,0.09)" stroke-width="6" fill="none" stroke-dasharray="18 8"></circle>
          <circle r="52" class="ring2" stroke="rgba(70,245,255,0.11)" stroke-width="3" fill="none" stroke-dasharray="7 12"></circle>
          <circle r="34" class="ring3" stroke="rgba(140,255,255,0.14)" stroke-width="2" fill="none" stroke-dasharray="2 9"></circle>
          <circle r="18" class="ring4" stroke="rgba(0,255,240,0.95)" stroke-width="2" fill="none"></circle> -->
        </g>
      </svg>
      <div class="label">
        <!-- <div style="font-weight:700">POWER SYSTEMS</div> -->
        <div style="font-size:12px;opacity:0.9" id="sat3sub"></div>
      </div>
    </div>

    <div class="sat" id="sat4" style="left:calc(50% + 360px); top:8%">
      <svg class="svg" viewBox="0 0 200 200">
        <g transform="translate(100,100)">
          <!-- <circle r="70" class="ring1" stroke="rgba(0,220,255,0.10)" stroke-width="6" fill="none" stroke-dasharray="22 12"></circle>
          <circle r="52" class="ring2" stroke="rgba(88,240,255,0.12)" stroke-width="3" fill="none" stroke-dasharray="9 16"></circle>
          <circle r="34" class="ring3" stroke="rgba(120,255,255,0.14)" stroke-width="2" fill="none" stroke-dasharray="5 8"></circle>
          <circle r="18" class="ring4" stroke="rgba(0,230,255,0.95)" stroke-width="2" fill="none"></circle> -->
        </g>
      </svg>
      <div class="label">
        <!-- <div style="font-weight:700">ACTUATOR CTRL</div> -->
        <div style="font-size:12px;opacity:0.9" id="sat4sub">—</div>
      </div>
    </div>

  </div>

  <!-- RIGHT: Research feed -->
  <div class="right-col">
    <div class="panel" style="height:100%">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
        <!-- <div style="font-weight:800">Research & Alerts</div> -->
        <div style="font-size:12px;opacity:0.8">live • network</div>
      </div>
      <div class="feed-list" id="feedList"></div>
    </div>
  </div>

  <!-- BOTTOM: Console -->
  <div class="panel console" style="grid-column:1/4;grid-row:2/3;display:flex;flex-direction:column" id="consolePanel">
    <div style="display:flex;justify-content:space-between;align-items:center">
      <!-- <div style="font-weight:800">SYSTEM CONSOLE</div> -->
      <div style="opacity:0.8;font-size:12px">Real-time telemetry</div>
    </div>
    <div style="flex:1;overflow:auto;padding-top:8px" id="consoleLog"></div>
  </div>
</div>

<script>
/* -------------------------
   Simulated realistic robotic state
   ------------------------- */

const state = {
  main: { load: 78.2, stability: 97.4, sync: 92.6, temp: 45.2 },
  ai:   { neural: 89.3, lr: 0.0013, err: 0.0032 },
  sensors:{ lidar: 96.4, thermal: 92.1, lat: 3.2 },
  power:{ output:86.3, battery:91.2, overload:false },
  actuators:{ torque:128.6, precision:97.7, cycles:124345 }
};

// slight random walk helpers
function rnd(min,max,dec=1){ return +(Math.random()*(max-min)+min).toFixed(dec) }
function clamp(v,a,b){ return Math.max(a,Math.min(b,v)) }

function tickState(){
  // main
  state.main.load = clamp(state.main.load + rnd(-1.2,1.5,2), 20, 100);
  state.main.stability = clamp(state.main.stability + rnd(-0.2,0.9,2), 80, 99.99);
  state.main.sync = clamp(state.main.sync + rnd(-0.6,0.6,2), 50, 100);
  state.main.temp = clamp(state.main.temp + rnd(-0.6,0.9,2), 25, 90);

  // ai
//   state.ai.neural = clamp(state.ai.neural + rnd(-1.3,1.1,2), 40, 100);
//   state.ai.lr = clamp(state.ai.lr * (1 + rnd(-0.02,0.02,4)), 0.00001, 0.01);
//   state.ai.err = clamp(state.ai.err * (1 + rnd(-0.05,0.05,4)), 0.0001, 0.05);

  // sensors
//   state.sensors.lidar = clamp(state.sensors.lidar + rnd(-0.8,0.7,2), 60, 100);
//   state.sensors.lat = clamp(state.sensors.lat * (1 + rnd(-0.03,0.03,3)), 0.5, 20);

  // power
//   state.power.output = clamp(state.power.output + rnd(-1.5,1.8,2), 30, 130);
//   state.power.battery = clamp(state.power.battery + rnd(-0.6,0.5,2), 10, 100);
//   state.power.overload = Math.random() < 0.01 ? !state.power.overload : state.power.overload;

  // actuators
//   state.actuators.torque = clamp(state.actuators.torque + rnd(-2,3,2), 60, 200);
//   state.actuators.precision = clamp(state.actuators.precision + rnd(-0.6,0.6,2), 70, 99.99);
//   state.actuators.cycles = Math.floor(state.actuators.cycles + rnd(0,20,0));
}

/* -------------------------
   Render center & satellites
   ------------------------- */
function renderCenter(){
  document.getElementById('mainCoreLoad').innerText = state.main.load.toFixed(1) + ' %';
  document.getElementById('mainCoreSub').innerText = `stability ${state.main.stability.toFixed(2)} • sync ${state.main.sync.toFixed(2)}%`;
  // satellites quick labels
//   document.getElementById('sat1sub').innerText = `Neural ${state.ai.neural.toFixed(1)}% · err ${state.ai.err.toFixed(3)}`;
//   document.getElementById('sat2sub').innerText = `Lidar ${state.sensors.lidar.toFixed(1)}% · ${state.sensors.lat.toFixed(2)}ms`;
//   document.getElementById('sat3sub').innerText = `Out ${state.power.output.toFixed(1)}% · Batt ${state.power.battery.toFixed(0)}%`;
//   document.getElementById('sat4sub').innerText = `Torque ${state.actuators.torque.toFixed(1)}Nm · ${state.actuators.precision.toFixed(1)}%`;
//   // left mini stats
  document.getElementById('statTemp').innerText = state.main.temp.toFixed(1) + ' °C';
  document.getElementById('statIntegrity').innerText = state.main.stability.toFixed(2) + ' %';
  document.getElementById('statEfficiency').innerText = (state.power.output * 0.92).toFixed(1) + ' %';
}

/* -------------------------
   Charts (canvas) - Bar & Donut
   ------------------------- */

const barCanvas = document.getElementById('barChart');
const barCtx = barCanvas.getContext('2d');

function drawBarChart(){
  const w = barCanvas.width, h = barCanvas.height;
  // clear
  barCtx.clearRect(0,0,w,h);
  // sample pairs: actuator torque baseline & current, AI load %
  const torque = state.actuators.torque; // e.g. 120
  const torqueBase = 120;
  const aiLoad = state.ai.neural; // %
  const maxTorque = 220;
  const maxAI = 100;
  // draw axes grid
  barCtx.strokeStyle = 'rgba(0,230,255,0.06)';
  for(let i=0;i<5;i++){
    const y = 10 + i*(h-30)/4;
    barCtx.beginPath(); barCtx.moveTo(40,y); barCtx.lineTo(w-10,y); barCtx.stroke();
  }
  // bars positions
  const left = 60;
  const bw = 36;
  // torque base (background)
  const tb = Math.min(1, torqueBase / maxTorque);
  const th = (h-40)*tb;
  barCtx.fillStyle = 'rgba(255,255,255,0.04)';
  barCtx.fillRect(left, h-20-th, bw, th);
  barCtx.fillStyle = 'rgba(0,220,255,0.18)';
  const tcur = Math.min(1, torque / maxTorque);
  barCtx.fillRect(left+4, h-20-(h-40)*tcur, bw-8, (h-40)*tcur);
  // AI load as line
  const x2 = left + 120;
  barCtx.fillStyle = 'rgba(255,255,255,0.06)';
  barCtx.fillRect(x2, h-20-(h-40)*(aiLoad/maxAI), bw, (h-40)*(aiLoad/maxAI));
  // labels
  barCtx.fillStyle = '#9ff';
  barCtx.font = '12px Inter, Arial';
  barCtx.fillText('Torque (Nm)', left-2, h-4);
  barCtx.fillText('AI Load %', x2-4, h-4);
  // numeric readouts overlay
  barCtx.fillStyle = 'rgba(0,230,255,0.95)';
  barCtx.font = '14px Inter, Arial';
  barCtx.fillText(torque.toFixed(1)+' Nm', left+2, h-28-(h-40)*tcur);
  barCtx.fillText(aiLoad.toFixed(1)+' %', x2+2, h-28-(h-40)*(aiLoad/maxAI));
}

const donutCanvas = document.getElementById('donutChart');
const donutCtx = donutCanvas.getContext('2d');
const donutLegend = document.getElementById('donutLegend');

function drawDonut(){
  const w = donutCanvas.width, h = donutCanvas.height, cx=w/2, cy=h/2, r=Math.min(w,h)/2 - 10;
  donutCtx.clearRect(0,0,w,h);
  // energy distribution: core / actuators / sensors / misc
  const core = Math.max(2, state.main.load);
  const actu = Math.max(2, (state.actuators.torque/200)*100); // scaled
  const sens = Math.max(2, state.sensors.lidar/1.2);
  const batt = Math.max(2, state.power.battery);
  const total = core + actu + sens + batt;
  const parts = [
    {label:'Core', value:core, color:'#00e6ff'},
    {label:'Actuators', value:actu, color:'#61f0a8'},
    {label:'Sensors', value:sens, color:'#77d4ff'},
    {label:'Battery', value:batt, color:'#6a9dff'}
  ];
  let angle = -Math.PI/2;
  donutLegend.innerHTML = '';
  parts.forEach(p=>{
    const slice = p.value/total;
    const next = angle + slice * Math.PI*2;
    donutCtx.beginPath();
    donutCtx.moveTo(cx,cy);
    donutCtx.arc(cx,cy,r, angle, next);
    donutCtx.closePath();
    donutCtx.fillStyle = p.color;
    donutCtx.fill();
    angle = next;
    // legend
    const el = document.createElement('div');
    el.innerHTML = `<span style="display:inline-block;width:12px;height:12px;background:${p.color};margin-right:6px;vertical-align:middle;border-radius:2px"></span>${p.label} ${Math.round(p.value)}%`;
    donutLegend.appendChild(el);
  });
  // inner hole
  donutCtx.beginPath(); donutCtx.fillStyle = 'rgba(0,0,0,0.6)'; donutCtx.arc(cx,cy,r*0.6,0,Math.PI*2); donutCtx.fill();
  // center text
  donutCtx.fillStyle = '#bff'; donutCtx.font = '12px Inter'; donutCtx.textAlign='center';
  donutCtx.fillText('Energy', cx, cy-4);
  donutCtx.fillStyle = '#9ff'; donutCtx.font = '13px Inter'; donutCtx.fillText(Math.round(core)+'%', cx, cy+14);
}

/* -------------------------
   Research feed & console
   ------------------------- */
const researchHeads = [
  "MIT: Neuromorphic accelerators reduce latency by 0.8ms",
  "DARPA: Swarm coordination test successful at 1200 units",
  "Graphene battery prototype achieves 1.6x density",
  "Sensor fusion update reduces false positives by 7.2%",
  "Actuator micro-alignment improved to 0.00012 mm",
  "AI core v13.3 deployed with adaptive learning decay"
];
function addFeed(){
  const f = document.getElementById('feedList');
  const item = document.createElement('div');
  item.className = 'feed-entry';
  item.innerText = researchHeads[Math.floor(Math.random()*researchHeads.length)];
  f.prepend(item);
  if(f.children.length>8) f.removeChild(f.lastChild);
}

/* -------------------------
   Console logging
   ------------------------- */
function addConsole(line){
  const box = document.getElementById('consoleLog');
  const el = document.createElement('div');
  el.className = 'log';
  el.textContent = `[${(new Date()).toLocaleTimeString()}] ${line}`;
  box.prepend(el);
  // keep recent N
  while(box.children.length > 80) box.removeChild(box.lastChild);
}

/* -------------------------
   Animation: rotating/SVG ring transforms
   ------------------------- */
/* We'll animate the center rings and satellite rings by CSS transforms on classes */
const style = document.createElement('style');
style.textContent = `
  /* main core rotation speeds */
  .ringA{ transform-origin: 50% 50%; animation: ringAspin 30s linear infinite; stroke: rgba(0,230,255,0.16); }
  .ringB{ transform-origin: 50% 50%; animation: ringBspin 18s linear reverse infinite; stroke: rgba(80,240,255,0.12); }
  .ringC{ transform-origin: 50% 50%; animation: ringCspin 12s linear infinite; stroke: rgba(160,255,255,0.14); }
  .ringD{ transform-origin: 50% 50%; animation: ringDspin 8s linear reverse infinite; stroke: rgba(0,230,255,0.9); }
  @keyframes ringAspin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
  @keyframes ringBspin{from{transform:rotate(0deg)}to{transform:rotate(-360deg)}}
  @keyframes ringCspin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
  @keyframes ringDspin{from{transform:rotate(0deg)}to{transform:rotate(-360deg)}}

  /* satellite ring animations */
  .ring1{ transform-origin:50% 50%; animation: r1 22s linear infinite; }
  .ring2{ transform-origin:50% 50%; animation: r2 16s linear reverse infinite; }
  .ring3{ transform-origin:50% 50%; animation: r3 12s linear infinite; }
  .ring4{ transform-origin:50% 50%; animation: r4 7s linear reverse infinite; }
  @keyframes r1{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
  @keyframes r2{from{transform:rotate(0deg)}to{transform:rotate(-360deg)}}
  @keyframes r3{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
  @keyframes r4{from{transform:rotate(0deg)}to{transform:rotate(-360deg)}}
`;
document.head.appendChild(style);

/* -------------------------
   Main update loop
   ------------------------- */
function updateAll(){
  tickState();
  renderCenter();
  drawBarChart();
  drawDonut();
}
setInterval(()=>{ updateAll(); addFeed(); addConsole(randomConsoleLine()); }, 3000);
updateAll();
addFeed();
addConsole('System init: dashboard active');

/* helper console messages (realistic) */
function randomConsoleLine(){
  const msgs = [
    `AI Core: gradient norm stable (${(Math.random()*0.01).toFixed(6)})`,
    `Sensors: lidar sweep complete - error < ${(Math.random()*0.5).toFixed(3)}%`,
    `Power: cell balancing engaged, cell temps ${ (state.main.temp+Math.random()*2).toFixed(1)}°C`,
    `Actuator: torque recalibrated to ${state.actuators.torque.toFixed(1)} Nm`,
    `Telemetry: packet loss ${ (Math.random()*0.2).toFixed(2)}%`,
    `Diagnostics: no critical faults detected`
  ];
  return msgs[Math.floor(Math.random()*msgs.length)];
}

/* Small responsive redraw on resize */
window.addEventListener('resize', ()=>{ drawBarChart(); drawDonut(); });

</script>
</body>
</html>
