document.getElementById('todayDate').textContent =
  new Date().toLocaleDateString('en-PH',{weekday:'long',year:'numeric',month:'long',day:'numeric'});
document.getElementById('menuToggle')?.addEventListener('click',()=>
  document.getElementById('sidebar').classList.toggle('sb-open'));
function toggleAvatarDropdown(e){e.stopPropagation();document.getElementById('avatarDropdown').classList.toggle('show');}
document.addEventListener('click',()=>document.getElementById('avatarDropdown')?.classList.remove('show'));

$.get('../php/get/get_feedback.php',{dept:DEPT_CODE,per_page:1,page:1},function(res){
  if(res.success) $('#sbFeedbackCount').text(res.summary.total||0);
});

function loadPredictions(){
  document.getElementById('spinnerOverlay').classList.add('show');

  $.ajax({
    url: '../php/get/get_predictive_data.php',
    method: 'GET',
    dataType: 'json',
    success(res){
      if(!res.success){alert('Error: '+(res.message||'Failed.'));return;}
      const d = res.data;

      // Health card
      $('#healthScore').text(d.summary.health_score+'%');
      $('#healthLabel').text(d.summary.health_label);
      $('#healthRec').text(d.summary.recommendation);
      $('#healthCard').show();

      renderPredCards(d);
      renderTrendChart(d.trend);
      renderRiskStatus(d.risk);
      renderSQDAnalysis(d.sqd);
      $('#recommendationText').text(d.summary.recommendation);

      $('#loadingCard').hide();
      $('#paContent').show();
    },
    error(xhr){console.error(xhr.responseText);},
    complete(){ document.getElementById('spinnerOverlay').classList.remove('show'); }
  });
}

function renderPredCards(d){
  const trend = d.trend;
  const dirIcon  = trend.direction==='improving'?'↑':trend.direction==='declining'?'↓':'→';
  const dirClass = trend.direction==='improving'?'up':trend.direction==='declining'?'down':'stable';
  const dirLabel = trend.direction==='improving'?'Improving':trend.direction==='declining'?'Declining':'Stable';

  const myRisk = d.risk.alerts[0];
  const riskLevel = myRisk ? myRisk.risk_level : 'none';
  const riskIcon  = riskLevel==='high'?'↓ High Risk':riskLevel==='moderate'?'⚠ Moderate':'✓ Low Risk';
  const riskClass = riskLevel==='high'?'down':riskLevel==='moderate'?'stable':'up';

  const weakCount = d.sqd.weak_count;
  const sqdClass  = weakCount>0?'down':d.sqd.improve_count>0?'stable':'up';
  const sqdIcon   = weakCount>0?`${weakCount} Weak`:d.sqd.improve_count>0?`${d.sqd.improve_count} To Improve`:'All Good';

  $('#predCards').html(`
    <div class="pred-card">
      <div class="pred-card-top">
        <div class="pred-icon blue"><i class="bi bi-graph-up-arrow"></i></div>
        <div class="pred-direction ${dirClass}">${dirIcon} ${dirLabel}</div>
      </div>
      <div class="pred-label">Satisfaction Trend</div>
      <div class="pred-value">${trend.wma_sat!==null?trend.wma_sat+'%':'—'}</div>
      <div class="pred-sub">Weighted moving average<br>Last 6 months</div>
      <div class="pred-forecast">
        <i class="bi bi-calendar-event" style="color:#1a6fbf"></i>
        Next month: <strong>${trend.forecast_sat!==null?trend.forecast_sat+'%':'Need more data'}</strong>
      </div>
    </div>
    <div class="pred-card">
      <div class="pred-card-top">
        <div class="pred-icon orange"><i class="bi bi-exclamation-triangle-fill"></i></div>
        <div class="pred-direction ${riskClass}">${riskIcon}</div>
      </div>
      <div class="pred-label">Risk Status</div>
      <div class="pred-value">${myRisk?myRisk.current_avg.toFixed(1)+'★':'N/A'}</div>
      <div class="pred-sub">Current avg rating<br>${myRisk?myRisk.declines+' consecutive decline(s)':'No significant decline'}</div>
      <div class="pred-forecast">
        <i class="bi bi-info-circle" style="color:#d04a02"></i>
        Based on <strong>decline detection analysis</strong>
      </div>
    </div>
    <div class="pred-card">
      <div class="pred-card-top">
        <div class="pred-icon purple"><i class="bi bi-clipboard-data-fill"></i></div>
        <div class="pred-direction ${sqdClass}">${sqdIcon}</div>
      </div>
      <div class="pred-label">SQD Health</div>
      <div class="pred-value">${d.sqd.overall_avg>0?d.sqd.overall_avg+'/5':'—'}</div>
      <div class="pred-sub">SQD overall average<br>${d.sqd.good_count} good · ${d.sqd.improve_count} to improve · ${d.sqd.weak_count} weak</div>
      <div class="pred-forecast">
        <i class="bi bi-info-circle" style="color:#6741d9"></i>
        Based on <strong>threshold analysis</strong>
      </div>
    </div>`);
}

function renderTrendChart(trend){
  if(!trend.monthly_data.length) return;
  const labels   = trend.monthly_data.map(m=>m.month_label);
  const satData  = trend.monthly_data.map(m=>parseFloat(m.satisfaction_rate)||0);

  if(trend.forecast_sat!==null){ labels.push('Next Month ★'); satData.push(trend.forecast_sat); }

  // Linear trend line
  const n=satData.length, indices=satData.map((_,i)=>i);
  const sumX=indices.reduce((a,b)=>a+b,0), sumY=satData.reduce((a,b)=>a+b,0);
  const sumXY=indices.reduce((s,x,i)=>s+x*satData[i],0), sumX2=indices.reduce((s,x)=>s+x*x,0);
  const slope=(n*sumXY-sumX*sumY)/(n*sumX2-sumX*sumX);
  const intercept=(sumY-slope*sumX)/n;
  const trendLine=indices.map(x=>Math.round((intercept+slope*x)*10)/10);

  if(chartTrend) chartTrend.destroy();
  chartTrend=new Chart(document.getElementById('chartTrend'),{
    type:'line',
    data:{labels,datasets:[
      {label:'Satisfaction Rate %',data:satData,borderColor:'#8B1A1A',backgroundColor:'rgba(139,26,26,.08)',borderWidth:2.5,tension:.35,fill:true,pointBackgroundColor:'#8B1A1A',pointRadius:4},
      {label:'Trend Line',data:trendLine,borderColor:'#1a6fbf',backgroundColor:'transparent',borderWidth:1.5,borderDash:[5,4],tension:0,fill:false,pointRadius:0}
    ]},
    options:{responsive:true,maintainAspectRatio:false,
      interaction:{mode:'index',intersect:false},
      plugins:{legend:{labels:{font:{size:11},boxWidth:12}}},
      scales:{y:{min:0,max:100,grid:{color:'rgba(0,0,0,.05)'},ticks:{callback:v=>v+'%'}},x:{grid:{display:false},ticks:{font:{size:10}}}}}
  });
}

function renderRiskStatus(risk){
  const myRisk = risk.alerts[0];
  const level  = myRisk ? myRisk.risk_level : 'none';
  const el     = document.getElementById('riskStatusBox');

  const configs = {
    high:     { icon: 'bi-exclamation-triangle-fill', title: 'High Risk', msg: `Your department's ratings have declined for ${myRisk?.declines||0} consecutive month(s). Immediate attention needed.`, color: '#c0392b' },
    moderate: { icon: 'bi-exclamation-circle-fill',   title: 'Moderate Risk', msg: `Rating showing a declining pattern. Monitor closely and take proactive measures.`, color: '#e67e22' },
    none:     { icon: 'bi-shield-check-fill',         title: 'No Risk Detected', msg: 'Your department is performing well with no significant declining trend.', color: '#1e7c3b' },
  };

  const cfg = configs[level] || configs.none;
  el.innerHTML = `
    <div class="risk-status ${level}">
      <i class="bi ${cfg.icon}" style="color:${cfg.color}"></i>
      <div>
        <div style="font-size:15px;font-weight:700;color:${cfg.color};margin-bottom:5px">${cfg.title}</div>
        <div style="font-size:13px;color:#555;line-height:1.5">${cfg.msg}</div>
        ${myRisk ? `<div style="margin-top:10px;font-size:12px;color:#888">
          Current avg: <strong>${myRisk.current_avg.toFixed(2)}</strong> ·
          Change: <strong style="color:${myRisk.change>=0?'#1e7c3b':'#c0392b'}">${myRisk.change>=0?'+':''}${myRisk.change}</strong>
        </div>` : ''}
      </div>
    </div>`;
}

function renderSQDAnalysis(sqd){
  const el = document.getElementById('sqdAnalysisList');
  if(!sqd.analysis.length){
    el.innerHTML='<p style="color:#bbb;text-align:center;padding:20px">No SQD data available. Collect more feedback first.</p>';
    return;
  }
  const statusLabels = {good:'Good',needs_improvement:'Needs Improvement',weak:'Weak',critical:'Critical'};
  const barColors = {good:'#1e7c3b',needs_improvement:'#b06c10',weak:'#c0392b',critical:'#7b1010'};

  el.innerHTML = '<div style="display:grid;grid-template-columns:1fr 1fr;gap:4px 24px">'+
    sqd.analysis.map(s=>`
      <div class="sqd-analysis-row">
        <div style="width:46px;font-size:11px;font-weight:700;color:#666;flex-shrink:0">${s.key.toUpperCase()}</div>
        <div class="sqd-analysis-label">${escHtml(s.label)}</div>
        <div class="sqd-analysis-bar-wrap"><div class="sqd-analysis-bar-fill" style="width:${s.pct}%;background:${barColors[s.status]}"></div></div>
        <div class="sqd-analysis-val" style="color:${barColors[s.status]}">${s.avg.toFixed(2)}</div>
        <div class="sqd-status-badge ${s.status}">${statusLabels[s.status]}</div>
      </div>`).join('') + '</div>'+
    `<div style="margin-top:12px;padding:10px 12px;background:#f8f8f8;border-radius:8px;font-size:11.5px;color:#555">
      <strong>Recommendation:</strong> ${sqd.analysis[0]?.recommendation||''}
    </div>`;
}

function escHtml(s){if(!s)return'';return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}

loadPredictions();