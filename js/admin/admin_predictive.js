
let chartTrend = null;

document.getElementById('todayDate').textContent =
  new Date().toLocaleDateString('en-PH',{weekday:'long',year:'numeric',month:'long',day:'numeric'});
document.getElementById('menuToggle')?.addEventListener('click',()=>
  document.getElementById('sidebar').classList.toggle('sb-open'));
function toggleAvatarDropdown(e){e.stopPropagation();document.getElementById('avatarDropdown').classList.toggle('show');}
document.addEventListener('click',()=>document.getElementById('avatarDropdown')?.classList.remove('show'));

function loadPredictions(){
  const dept = document.getElementById('filterDept').value;
  const btn  = document.getElementById('loadBtn');

  document.getElementById('spinnerOverlay').classList.add('show');
  btn.disabled = true;
  btn.innerHTML = '<i class="bi bi-hourglass-split spin-anim"></i> Processing…';

  $.ajax({
    url: '../php/get/get_predictive_data.php',
    method: 'GET',
    data: { dept },
    dataType: 'json',
    success(res){
      if (!res.success){ alert('Error: '+(res.message||'Failed.')); return; }
      const d = res.data;

      renderHealthCard(d.summary);
      renderPredCards(d);
      renderTrendChart(d.trend);
      renderRiskAlerts(d.risk);
      renderSQDAnalysis(d.sqd);
      renderRecommendation(d.summary);

      document.getElementById('emptyState').style.display = 'none';
      document.getElementById('paContent').style.display  = 'block';
      document.getElementById('healthCard').style.display = 'block';
    },
    error(xhr){ console.error(xhr.responseText); alert('Server error.'); },
    complete(){
      document.getElementById('spinnerOverlay').classList.remove('show');
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-graph-up-arrow"></i> Load Predictions';
    }
  });
}

function renderHealthCard(summary){
  const score = summary.health_score;
  const color = score >= 80 ? '#1e7c3b' : score >= 65 ? '#b06c10' : '#c0392b';
  $('#healthScore').text(score + '%');
  $('#healthLabel').text(summary.health_label);
  $('#healthRec').text(summary.recommendation);
}

function renderPredCards(d){
  const trend = d.trend;
  const dirIcon  = trend.direction==='improving'?'↑':trend.direction==='declining'?'↓':'→';
  const dirClass = trend.direction==='improving'?'up':trend.direction==='declining'?'down':'stable';
  const dirLabel = trend.direction==='improving'?'Improving':trend.direction==='declining'?'Declining':'Stable';

  const highRisk = d.risk.high_risk_count;
  const modRisk  = d.risk.mod_risk_count;
  const riskLabel = highRisk > 0 ? `${highRisk} High Risk` : modRisk > 0 ? `${modRisk} Moderate Risk` : 'All Clear';
  const riskClass = highRisk > 0 ? 'down' : modRisk > 0 ? 'stable' : 'up';

  const weakCount = d.sqd.weak_count;
  const sqdClass  = weakCount > 0 ? 'down' : d.sqd.improve_count > 0 ? 'stable' : 'up';
  const sqdLabel  = weakCount > 0 ? `${weakCount} Weak Dimension${weakCount>1?'s':''}` : d.sqd.improve_count > 0 ? `${d.sqd.improve_count} Need Improvement` : 'All Dimensions Good';

  document.getElementById('predCards').innerHTML = `
    <!-- Forecast Card -->
    <div class="pred-card">
      <div class="pred-card-top">
        <div class="pred-icon blue"><i class="bi bi-graph-up-arrow"></i></div>
        <div class="pred-direction ${dirClass}">${dirIcon} ${dirLabel}</div>
      </div>
      <div class="pred-label">Satisfaction Trend</div>
      <div class="pred-value">${trend.wma_sat !== null ? trend.wma_sat+'%' : '—'}</div>
      <div class="pred-sub">Weighted moving average<br>Last 6 months of data</div>
      <div class="pred-forecast">
        <i class="bi bi-calendar-event" style="color:#1a6fbf"></i>
        Next month forecast: <strong>${trend.forecast_sat !== null ? trend.forecast_sat+'%' : 'Insufficient data'}</strong>
      </div>
    </div>

    <!-- Risk Card -->
    <div class="pred-card">
      <div class="pred-card-top">
        <div class="pred-icon orange"><i class="bi bi-exclamation-triangle-fill"></i></div>
        <div class="pred-direction ${riskClass}">${riskLabel}</div>
      </div>
      <div class="pred-label">Department Risk Status</div>
      <div class="pred-value">${highRisk + modRisk}</div>
      <div class="pred-sub">Departments flagged<br>${highRisk} high risk · ${modRisk} moderate</div>
      <div class="pred-forecast">
        <i class="bi bi-info-circle" style="color:#d04a02"></i>
        Based on <strong>consecutive decline detection</strong>
      </div>
    </div>

    <!-- SQD Card -->
    <div class="pred-card">
      <div class="pred-card-top">
        <div class="pred-icon purple"><i class="bi bi-clipboard-data-fill"></i></div>
        <div class="pred-direction ${sqdClass}">${sqdLabel}</div>
      </div>
      <div class="pred-label">SQD Health</div>
      <div class="pred-value">${d.sqd.overall_avg > 0 ? d.sqd.overall_avg+'/5' : '—'}</div>
      <div class="pred-sub">Overall SQD average<br>${d.sqd.good_count} good · ${d.sqd.improve_count} needs improvement · ${d.sqd.weak_count} weak</div>
      <div class="pred-forecast">
        <i class="bi bi-info-circle" style="color:#6741d9"></i>
        Based on <strong>threshold analysis</strong> (last 3 months)
      </div>
    </div>`;
}

function renderTrendChart(trend){
  if (!trend.monthly_data.length) return;

  const labels   = trend.monthly_data.map(m => m.month_label);
  const satData  = trend.monthly_data.map(m => parseFloat(m.satisfaction_rate)||0);
  const ratingData = trend.monthly_data.map(m => parseFloat(m.avg_rating)||0);

  // Forecast point
  const forecastLabel = 'Next Month (Forecast)';
  if (trend.forecast_sat !== null){
    labels.push(forecastLabel);
    satData.push(trend.forecast_sat);
    ratingData.push(trend.wma_rating);
  }

  // Trend line (linear regression on sat data)
  const n = satData.length;
  const indices = satData.map((_,i)=>i);
  const sumX = indices.reduce((a,b)=>a+b,0);
  const sumY = satData.reduce((a,b)=>a+b,0);
  const sumXY = indices.reduce((s,x,i)=>s+x*satData[i],0);
  const sumX2 = indices.reduce((s,x)=>s+x*x,0);
  const slope = (n*sumXY - sumX*sumY) / (n*sumX2 - sumX*sumX);
  const intercept = (sumY - slope*sumX) / n;
  const trendLine = indices.map(x => Math.round((intercept + slope*x)*10)/10);

  if (chartTrend) chartTrend.destroy();
  chartTrend = new Chart(document.getElementById('chartTrend'),{
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'Satisfaction Rate %',
          data: satData,
          borderColor: '#8B1A1A',
          backgroundColor: 'rgba(139,26,26,.08)',
          borderWidth: 2.5, tension: .35, fill: true,
          pointBackgroundColor: satData.map((_,i)=> i===satData.length-1&&trend.forecast_sat!==null ? '#1a6fbf' : '#8B1A1A'),
          pointRadius: satData.map((_,i)=> i===satData.length-1&&trend.forecast_sat!==null ? 6 : 4),
          pointStyle: satData.map((_,i)=> i===satData.length-1&&trend.forecast_sat!==null ? 'star' : 'circle'),
        },
        {
          label: 'Trend Line',
          data: trendLine,
          borderColor: '#1a6fbf',
          backgroundColor: 'transparent',
          borderWidth: 1.5, borderDash: [5,4], tension: 0, fill: false,
          pointRadius: 0,
        }
      ]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { labels: { font: { size: 11 }, boxWidth: 12 } },
        tooltip: {
          callbacks: {
            label: (item) => `${item.dataset.label}: ${item.parsed.y}${item.dataset.label.includes('%')?'%':''}`
          }
        }
      },
      scales: {
        y: { min: 0, max: 100, grid: { color: 'rgba(0,0,0,.05)' }, ticks: { callback: v => v+'%' } },
        x: { grid: { display: false }, ticks: { font: { size: 10 } } }
      }
    }
  });
}

function renderRiskAlerts(risk){
  const el = document.getElementById('riskAlertsList');
  if (!risk.alerts.length) {
    el.innerHTML = `<div style="padding:20px;text-align:center">
      <i class="bi bi-shield-check" style="font-size:32px;color:#1e7c3b;display:block;margin-bottom:8px;opacity:.6"></i>
      <p style="font-size:13px;color:#aaa">No departments at risk. All performing well! ✅</p>
    </div>`;
    return;
  }

  el.innerHTML = risk.alerts.map(a => {
    const changeStr = a.change >= 0 ? `+${a.change}` : `${a.change}`;
    const changeColor = a.change >= 0 ? '#1e7c3b' : '#c0392b';
    return `
      <div class="risk-item">
        <div class="risk-dot ${a.risk_level}"></div>
        <div style="flex:1">
          <div class="risk-name">${escHtml(a.dept_name)}</div>
          <div class="risk-meta">
            ${a.declines} consecutive decline${a.declines!==1?'s':''} ·
            <span style="color:${changeColor};font-weight:600">${changeStr} change</span>
          </div>
        </div>
        <div class="risk-avg" style="color:${a.current_avg>=4?'#1e7c3b':a.current_avg>=3?'#b06c10':'#c0392b'}">
          ${a.current_avg.toFixed(1)}
        </div>
        <div class="risk-badge ${a.risk_level}">
          ${a.risk_level.charAt(0).toUpperCase()+a.risk_level.slice(1)} Risk
        </div>
      </div>`;
  }).join('');
}

function renderSQDAnalysis(sqd){
  const el = document.getElementById('sqdAnalysisList');
  if (!sqd.analysis.length) {
    el.innerHTML = '<p style="color:#bbb;text-align:center;padding:20px">No SQD data available for the selected period.</p>';
    return;
  }

  const statusLabels = {
    good: 'Good',
    needs_improvement: 'Needs Improvement',
    weak: 'Weak',
    critical: 'Critical'
  };
  const barColors = {
    good: '#1e7c3b',
    needs_improvement: '#b06c10',
    weak: '#c0392b',
    critical: '#7b1010'
  };

  el.innerHTML = '<div style="display:grid;grid-template-columns:1fr 1fr;gap:4px 24px">' +
    sqd.analysis.map(s => `
      <div class="sqd-analysis-row">
        <div style="width:46px;font-size:11px;font-weight:700;color:#666;flex-shrink:0">${s.key.toUpperCase()}</div>
        <div class="sqd-analysis-label">${escHtml(s.label)}</div>
        <div class="sqd-analysis-bar-wrap">
          <div class="sqd-analysis-bar-fill" style="width:${s.pct}%;background:${barColors[s.status]}"></div>
        </div>
        <div class="sqd-analysis-val" style="color:${barColors[s.status]}">${s.avg.toFixed(2)}</div>
        <div class="sqd-status-badge ${s.status}">${statusLabels[s.status]}</div>
      </div>`).join('') + '</div>' +
    `<div style="margin-top:14px;padding:12px 14px;background:#f8f8f8;border-radius:8px;font-size:12px;color:#555">
      <strong>Legend:</strong>
      <span style="color:#1e7c3b;margin-left:12px">● Good (≥4.0)</span>
      <span style="color:#b06c10;margin-left:12px">● Needs Improvement (3.5–3.99)</span>
      <span style="color:#c0392b;margin-left:12px">● Weak (3.0–3.49)</span>
      <span style="color:#7b1010;margin-left:12px">● Critical (&lt;3.0)</span>
    </div>`;
}

function renderRecommendation(summary){
  document.getElementById('recommendationText').textContent = summary.recommendation;
}

function escHtml(s){
  if(!s) return '';
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// Auto-load on page open
loadPredictions();