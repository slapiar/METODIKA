<?php
$title = 'METODIKA | DB capability';
$activeNav = 'diagnostics';
$showFooter = false;
?>
<?= $this->extend('layouts/app') ?>
<?= $this->section('meta') ?>
<meta name="robots" content="noindex,nofollow,noarchive">
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<section class="panel">
<h1>Diagnostika databazy METODIKA</h1>
<table class="table">
<tr><td>Externe prostredie nacitane</td><td class="<?= $externalEnvironmentLoaded ? 'ok' : 'bad' ?>"><?= $externalEnvironmentLoaded ? 'ANO' : 'NIE' ?></td></tr>
<tr><td>Databazove spojenie</td><td class="<?= $inspection['connection'] ? 'ok' : 'bad' ?>"><?= $inspection['connection'] ? 'OK' : 'NIE' ?></td></tr>
<tr><td>Databazovy server</td><td><?= esc($inspection['serverVersion'] !== '' ? $inspection['serverVersion'] : 'NEZISTENA') ?></td></tr>
<tr><td>InnoDB</td><td class="<?= $inspection['innodb'] ? 'ok' : 'bad' ?>"><?= $inspection['innodb'] ? 'OK' : 'NIE' ?></td></tr>
<tr><td>utf8mb4_bin</td><td class="<?= $inspection['utf8mb4Bin'] ? 'ok' : 'bad' ?>"><?= $inspection['utf8mb4Bin'] ? 'OK' : 'NIE' ?></td></tr>
<tr><td>DATETIME(6)</td><td class="<?= $inspection['datetime6'] ? 'ok' : 'bad' ?>"><?= $inspection['datetime6'] ? 'OK' : 'NIE' ?></td></tr>
<tr><td>Celkovy vysledok</td><td class="<?= $overallReady ? 'ok' : 'bad' ?>"><?= $overallReady ? 'PRIPRAVENE' : 'NEPRIPRAVENE' ?></td></tr>
<tr><td>Cas diagnostiky</td><td><?= esc((string) $inspection['diagnosedAt']) ?></td></tr>
</table>
<div class="actions">
<form method="post" action="<?= site_url('diagnostics/database/run') ?>"><?= csrf_field() ?><button type="submit">Spustit znovu</button></form>
<form method="post" action="<?= site_url('diagnostics/database/logout') ?>"><?= csrf_field() ?><button type="submit">Odhlasit diagnostiku</button></form>
</div>
<p class="notice">Diagnostika je iba citacia. Migracie neboli spustene.</p>
<?php if (($concurrencyWebEnabled ?? false) === true): ?>
<hr class="diag-separator">
<section class="diag-concurrency" aria-labelledby="diag-concurrency-title">
<h2 id="diag-concurrency-title">Webove subezne overenie</h2>
<p class="diag-help">Spusti jeden diagnosticky run. Stranka odosle dva paralelne requesty a vyhodnoti vysledok bez zobrazenia citlivych internych detailov.</p>
<div class="actions"><button id="diag-concurrency-start" type="button">Start</button></div>
<p id="diag-concurrency-status" class="diag-status" role="status" aria-live="polite">Pripravene.</p>
<div id="diag-progress-wrap" class="diag-progress-wrap" hidden><div><span>Priebeh</span><strong id="diag-progress-label">0 %</strong></div><progress id="diag-progress" max="100" value="0">0 %</progress></div>
<div id="diag-log-wrap" class="diag-log-wrap" hidden><strong>Priebeh vykonavania</strong><ol id="diag-log" class="diag-log" aria-live="polite"></ol></div>
<dl class="diag-http" id="diag-http" hidden>
<div><dt>START</dt><dd id="diag-http-start">NEODOSLANE</dd></div><div><dt>HIT A</dt><dd id="diag-http-hit-a">NEODOSLANE</dd></div><div><dt>HIT B</dt><dd id="diag-http-hit-b">NEODOSLANE</dd></div><div><dt>RESULT</dt><dd id="diag-http-result">NEODOSLANE</dd></div>
</dl>
<dl class="diag-axes" id="diag-axes" hidden>
<div><dt>DB unikatnost</dt><dd id="diag-axis-db">NEPOTVRDENE</dd></div><div><dt>Aplikacny replay</dt><dd id="diag-axis-replay">NEPOTVRDENE</dd></div><div><dt>Cleanup</dt><dd id="diag-axis-cleanup">NEPOTVRDENE</dd></div><div><dt>Celkove</dt><dd id="diag-axis-overall">NEPOTVRDENE</dd></div>
</dl>
<div id="diag-participants" class="diag-participants" hidden><p><strong>A:</strong> <span id="diag-participant-a">-</span></p><p><strong>B:</strong> <span id="diag-participant-b">-</span></p></div>
</section>
<?php endif; ?>
</section>
<?= $this->endSection() ?>
<?php if (($concurrencyWebEnabled ?? false) === true): ?>
<?= $this->section('styles') ?>
<style>
.diag-separator{margin:1.5rem 0;border:0;border-top:1px solid #d8e0ec}.diag-help{color:#4b5c75}.diag-status{margin-top:.8rem;font-weight:600}.diag-progress-wrap{margin-top:1rem}.diag-progress-wrap div{display:flex;justify-content:space-between;margin-bottom:.3rem}.diag-progress-wrap progress{width:100%;height:1rem}.diag-log-wrap{margin-top:1rem;padding:.7rem;border:1px solid #d8e0ec;border-radius:.5rem;background:#f8fafc}.diag-log{max-height:14rem;overflow:auto;margin:.5rem 0 0;padding-left:1.4rem;font:13px Consolas,monospace}.diag-log li{margin:.2rem 0}.diag-log-ok{color:#166534}.diag-log-error{color:#991b1b;font-weight:700}.diag-http,.diag-axes{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:.6rem;margin:1rem 0 0;padding:0}.diag-http div,.diag-axes div{border:1px solid #d8e0ec;border-radius:.5rem;background:#eef2f7;padding:.7rem}.diag-http dt,.diag-axes dt{font-size:.82rem;color:#4b5c75}.diag-http dd,.diag-axes dd{margin:.25rem 0 0;font-weight:700}.diag-pass{color:#166534}.diag-fail{color:#991b1b}.diag-pending{color:#4b5c75}.diag-participants{margin-top:.8rem;font-size:.9rem;color:#10213b}
</style>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script nonce="<?= esc((string) ($scriptNonce ?? '')) ?>">
(function(){
const byId=(id)=>document.getElementById(id),startButton=byId('diag-concurrency-start'),statusNode=byId('diag-concurrency-status'),progressWrap=byId('diag-progress-wrap'),progress=byId('diag-progress'),progressLabel=byId('diag-progress-label'),logWrap=byId('diag-log-wrap'),logNode=byId('diag-log'),httpDiagnostics=byId('diag-http'),httpStart=byId('diag-http-start'),httpHitA=byId('diag-http-hit-a'),httpHitB=byId('diag-http-hit-b'),httpResult=byId('diag-http-result'),axes=byId('diag-axes'),axisDb=byId('diag-axis-db'),axisReplay=byId('diag-axis-replay'),axisCleanup=byId('diag-axis-cleanup'),axisOverall=byId('diag-axis-overall'),participants=byId('diag-participants'),participantA=byId('diag-participant-a'),participantB=byId('diag-participant-b');
if(!startButton||!progress||!logNode)return;
const csrfName=<?= json_encode(csrf_token(), JSON_UNESCAPED_SLASHES) ?>,csrfHash=<?= json_encode(csrf_hash(), JSON_UNESCAPED_SLASHES) ?>,startUrl=<?= json_encode(site_url('diagnostics/concurrency/start'), JSON_UNESCAPED_SLASHES) ?>,hitAUrl=<?= json_encode(site_url('diagnostics/concurrency/hit/a'), JSON_UNESCAPED_SLASHES) ?>,hitBUrl=<?= json_encode(site_url('diagnostics/concurrency/hit/b'), JSON_UNESCAPED_SLASHES) ?>,resultBaseUrl=<?= json_encode(site_url('diagnostics/concurrency/result'), JSON_UNESCAPED_SLASHES) ?>;
const setProgress=(n,t)=>{progress.value=n;progressLabel.textContent=t||n+' %'},addLog=(m,type='')=>{const li=document.createElement('li');li.textContent=new Date().toLocaleTimeString()+' — '+m;li.className=type==='ok'?'diag-log-ok':type==='error'?'diag-log-error':'';logNode.appendChild(li)},setHttp=(n,t,ok=null)=>{n.textContent=t;n.className=ok===true?'diag-pass':ok===false?'diag-fail':'diag-pending'},setAxis=(n,v)=>{n.textContent=v===true?'POTVRDENE':v===false?'NEPOTVRDENE':'NEZNAME';n.className=v===true?'diag-pass':'diag-fail'};
const parseJson=async(r,step)=>{addLog(step+': Content-Type '+(r.headers.get('content-type')||'neuvedeny'));try{return await r.json()}catch(e){addLog(step+': odpoved nie je platny JSON.','error');throw new Error(step.toLowerCase()+'-json-invalid')}};
const resultPoll=async(runId)=>{for(let i=0;i<24;i++){addLog('RESULT: pokus '+(i+1)+' z 24');let r;try{r=await fetch(resultBaseUrl+'/'+encodeURIComponent(runId),{credentials:'same-origin',cache:'no-store'})}catch(e){setHttp(httpResult,'SIETOVA CHYBA',false);throw new Error('result-network')}setHttp(httpResult,'HTTP '+r.status,r.status===200?true:null);if(r.status===200)return parseJson(r,'RESULT');await new Promise(x=>setTimeout(x,300))}setHttp(httpResult,'TIMEOUT',false);throw new Error('result-timeout')};
startButton.addEventListener('click',async()=>{startButton.disabled=true;httpDiagnostics.hidden=false;progressWrap.hidden=false;logWrap.hidden=false;axes.hidden=true;participants.hidden=true;logNode.replaceChildren();setProgress(0);setHttp(httpStart,'CAKAM');setHttp(httpHitA,'NEODOSLANE');setHttp(httpHitB,'NEODOSLANE');setHttp(httpResult,'NEODOSLANE');addLog('Zaciatok diagnostiky.');
try{statusNode.textContent='Pripravujem diagnosticky run...';setProgress(10,'START');addLog('START: odosielam poziadavku.');const body=new URLSearchParams();body.set(csrfName,csrfHash);let sr;try{sr=await fetch(startUrl,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/x-www-form-urlencoded;charset=UTF-8'},body:body.toString()})}catch(e){setHttp(httpStart,'SIETOVA CHYBA',false);throw new Error('start-network')}setHttp(httpStart,'HTTP '+sr.status,sr.status===200);addLog('START: HTTP '+sr.status,sr.status===200?'ok':'error');if(sr.status!==200)throw new Error('start-http-'+sr.status);setProgress(25,'Overujem START');const started=await parseJson(sr,'START');if(!started||typeof started!=='object')throw new Error('start-invalid-object');if(typeof started.runId!=='string'||!started.runId){setHttp(httpStart,'HTTP 200 / CHYBA runId',false);throw new Error('start-missing-runId')}if(typeof started.participantTokenA!=='string'||!started.participantTokenA){setHttp(httpStart,'HTTP 200 / CHYBA TOKEN A',false);throw new Error('start-missing-token-a')}if(typeof started.participantTokenB!=='string'||!started.participantTokenB){setHttp(httpStart,'HTTP 200 / CHYBA TOKEN B',false);throw new Error('start-missing-token-b')}setHttp(httpStart,'HTTP 200 / JSON OK',true);addLog('START: odpoved je kompletna.','ok');
statusNode.textContent='Odosielam paralelne HIT A/B...';setProgress(40,'HIT A/B');setHttp(httpHitA,'CAKAM');setHttp(httpHitB,'CAKAM');addLog('HIT A/B: odosielam paralelne poziadavky.');const hit=async(url,token,node,label)=>{const b=new URLSearchParams();b.set('runId',started.runId);b.set('participantToken',token);let r;try{r=await fetch(url,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/x-www-form-urlencoded;charset=UTF-8'},body:b.toString()})}catch(e){setHttp(node,'SIETOVA CHYBA',false);throw new Error(label.toLowerCase()+'-network')}setHttp(node,'HTTP '+r.status,r.status===200);addLog(label+': HTTP '+r.status,r.status===200?'ok':'error');return r};const hr=await Promise.all([hit(hitAUrl,started.participantTokenA,httpHitA,'HIT A'),hit(hitBUrl,started.participantTokenB,httpHitB,'HIT B')]);if(hr[0].status!==200||hr[1].status!==200)throw new Error('hit-failed');
statusNode.textContent='Nacitavam vysledok...';setProgress(70,'RESULT');setHttp(httpResult,'CAKAM');const result=await resultPoll(started.runId),a=result&&typeof result==='object'?result.assertions:null;setAxis(axisDb,a&&a.dbUniquenessConfirmed===true);setAxis(axisReplay,a&&a.appReplayConfirmed===true);setAxis(axisCleanup,a&&a.cleanupConfirmed===true);setAxis(axisOverall,a&&a.overallSuccess===true);const summary=(s)=>s&&typeof s==='object'?((s.outcome||'bez-vysledku')+(s.errorCode?' ('+s.errorCode+')':'')):'nezname';participantA.textContent=summary(result.participants?result.participants.a:null);participantB.textContent=summary(result.participants?result.participants.b:null);axes.hidden=false;participants.hidden=false;setProgress(100,'Dokoncene');statusNode.textContent='Hotovo. Stav runu: '+(typeof result.state==='string'?result.state:'UNKNOWN');addLog('Diagnostika dokoncena.','ok')}
catch(e){const code=e instanceof Error?e.message:'unknown';statusNode.textContent='Beh zlyhal alebo vyprsal. Pozri posledny zapis priebehu.';progressLabel.textContent='ZASTAVENE';addLog('Diagnostika zastavena: '+code+'.','error')}finally{startButton.disabled=false}});
})();
</script>
<?= $this->endSection() ?>
<?php endif; ?>