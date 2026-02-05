<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>RTS Deformasi 3D (Three.js)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="https://demo.monitoring4system.com/image/logo_single.png">
  <style>
    :root{--bg:#f6f7fb;--card:rgba(255,255,255,.92);--border:rgba(15,23,42,.10);--ink:#0f172a;--muted:#475569;--soft:#334155}
    body{background:var(--bg)}
    .glass{background:var(--card);border:1px solid var(--border);backdrop-filter:blur(10px);box-shadow:0 10px 30px rgba(15,23,42,.08)}
    .mono{font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace}
    .form-control,.form-select{background:#fff!important;border-color:rgba(15,23,42,.16)!important;color:var(--ink)!important}
    .form-label{color:var(--soft)}
    .text-soft{color:var(--muted)}
    .btn-grad{border:1px solid rgba(15,23,42,.14);background:linear-gradient(135deg,rgba(59,130,246,.18),rgba(16,185,129,.10));color:var(--ink)}
    .btn-grad:disabled{opacity:.6}
    .logbox{white-space:pre-wrap;max-height:260px;overflow:auto}
    .plot-wrap{position:relative}
    .fs-btn{position:absolute;left:12px;top:12px;z-index:7}
    .fs-btn .btn{background:rgba(255,255,255,.92);border:1px solid rgba(15,23,42,.14);color:var(--ink);backdrop-filter:blur(8px)}
    .fullscreen-target{background:var(--bg)}
    .fullscreen-target:fullscreen{background:var(--bg)}
    .viewport3d{width:100%;height:78vh;min-height:520px;border-radius:16px;overflow:hidden}
    .fullscreen-target:fullscreen .viewport3d{height:100vh;min-height:100vh}
    .hud{position:absolute;right:12px;top:12px;z-index:8;display:flex;gap:8px;align-items:center}
    .chip{background:rgba(255,255,255,.92);border:1px solid rgba(15,23,42,.14);border-radius:999px;padding:6px 10px;font-size:12px;color:var(--ink)}
  </style>

  <script type="importmap">
  {
    "imports": {
      "three": "https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js",
      "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.160.0/examples/jsm/"
    }
  }
  </script>
</head>

<body>
  <div class="container-fluid py-3">
    <div class="row g-3">
      <div class="col-12 col-lg-3">
        <div class="card glass rounded-4">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <a href="<?= base_url() ?>beranda" class="me-2 mb-0 pb-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                  <path d="M12 15v3.586a1 1 0 0 1 -1.707 .707l-6.586 -6.586a1 1 0 0 1 0 -1.414l6.586 -6.586a1 1 0 0 1 1.707 .707v3.586h6v6h-6z" />
                  <path d="M21 15v-6" />
                </svg>
              </a>
              <h5 class="mb-0 fw-bold" style="color:var(--ink)">Deformasi RTS 3D</h5>
            </div>

            <div class="mt-2">
              <label class="form-label small mb-1">Pilih Tanggal</label>
              <select id="tanggal" name="tanggal" class="form-select form-select-sm">
                <option disabled>Pilih Tanggal</option>
                <?php foreach($log_data as $k => $v) { ?>
                <option value="<?= $v['id_log'] ?>" <?= $this->session->userdata('temp_kontrol')->id_log == $v['id_log'] ? 'selected' : '' ?>>
                  <?= $v['datetime']?> (<?= $v['site'] == 'ccp' ? 'CPP3': 'VP' ?>)
                </option>
                <?php } ?>
              </select>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-12 col-md-4">
                <label class="form-label small mb-1">RTS E</label>
                <input id="rtsE" class="form-control form-control-sm" type="number" step="0.001">
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label small mb-1">RTS N</label>
                <input id="rtsN" class="form-control form-control-sm" type="number" step="0.001">
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label small mb-1">RTS Z</label>
                <input id="rtsZ" class="form-control form-control-sm" type="number" step="0.001">
              </div>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-12 col-md-6">
                <label class="form-label small mb-1">Vector Scale</label>
                <input id="vecScale" class="form-control form-control-sm" type="number" step="0.1" value="20">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label small mb-1">Threshold Linear</label>
                <input id="minLinear" class="form-control form-control-sm" type="number" step="0.0001" value="0">
              </div>
            </div>

            <div class="d-grid mt-3">
              <button id="btnLoad" class="btn btn-grad btn-sm fw-bold">Load</button>
            </div>
            <div class="d-grid mt-2">
              <button id="btnRender" class="btn btn-grad btn-sm fw-bold" disabled>Render 3D</button>
            </div>

            <div class="mt-3">
              <div class="mono border rounded-3 p-2 logbox glass" id="log"
                style="border-color:rgba(15,23,42,.12)!important;color:var(--ink)"></div>
            </div>

          </div>
        </div>
      </div>

      <div class="col-12 col-lg-9">
        <div class="card glass rounded-4">
          <div class="card-body p-2 plot-wrap fullscreen-target" id="fsTarget">
            <div class="fs-btn">
              <button id="btnFS" class="btn btn-sm fw-semibold">Fullscreen</button>
            </div>
            <div class="hud">
              <div class="chip" id="hudStat">-</div>
            </div>
            <div id="viewport" class="viewport3d"></div>
          </div>
          <div class="card-footer small text-soft" style="border-top:1px solid rgba(15,23,42,.10)!important">
            Pilih tanggal → Load → Render.
          </div>
        </div>
      </div>
    </div>
  </div>

  <script type="module">
    import * as THREE from "three"
    import { OrbitControls } from "three/addons/controls/OrbitControls.js"

    const baseUrl = "<?= base_url() ?>"

    const elTanggal = document.getElementById("tanggal")
    const elLog = document.getElementById("log")
    const elBtnLoad = document.getElementById("btnLoad")
    const elBtnRender = document.getElementById("btnRender")
    const elE = document.getElementById("rtsE")
    const elN = document.getElementById("rtsN")
    const elZ = document.getElementById("rtsZ")
    const elVecScale = document.getElementById("vecScale")
    const elMinLinear = document.getElementById("minLinear")
    const elBtnFS = document.getElementById("btnFS")
    const elFsTarget = document.getElementById("fsTarget")
    const elViewport = document.getElementById("viewport")
    const elHudStat = document.getElementById("hudStat")

    let payload = null

    function log(msg){
      elLog.textContent = (elLog.textContent ? elLog.textContent + "\n" : "") + msg
      elLog.scrollTop = elLog.scrollHeight
    }

    function toNum(v){
      if(v === null || v === undefined) return NaN
      if(typeof v === "number") return v
      const s = String(v).trim()
      if(!s) return NaN
      const n = Number(s.replace(/,/g,".").replace(/[^0-9\.\-]/g,""))
      return Number.isFinite(n) ? n : NaN
    }

    function isZeroish(a){ return Number.isFinite(a) && Math.abs(a) < 1e-12 }
    function isTripletAllZero(a,b,c){ return isZeroish(a) && isZeroish(b) && isZeroish(c) }

    function getRTSFromPayload(p){
      const r = p && p.posisi_rts ? p.posisi_rts : null
      if(!r) return null
      const E = toNum(r.E)
      const N = toNum(r.N)
      const Z = Number.isFinite(toNum(r.Z)) ? toNum(r.Z) : 0
      if(Number.isFinite(E) && Number.isFinite(N)) return { e:E, n:N, z:Z }
      const n = toNum(r.x)
      const e = toNum(r.y)
      if(!Number.isFinite(e) || !Number.isFinite(n)) return null
      return { e, n, z:0 }
    }

    function extractPoints(p){
      const arr = (p && p.data_pengukuran) ? p.data_pengukuran : []
      const out = []
      for(const row of arr){
        const t = row && row.temp_tembak ? row.temp_tembak : row
        if(!t) continue

        const name = row.nama_prisma ? String(row.nama_prisma) : (t.nama_prisma ? String(t.nama_prisma) : "")
        const e0 = toNum(t.E0), n0 = toNum(t.N0), z0 = toNum(t.Z0)
        if(![e0,n0,z0].every(Number.isFinite)) continue

        let e1 = toNum(t.E1), n1 = toNum(t.N1), z1 = toNum(t.Z1)
        const ok = [e1,n1,z1].every(Number.isFinite) && !isTripletAllZero(e1,n1,z1)

        let de = toNum(t.DE), dn = toNum(t.DN), dz = toNum(t.DZ)
        if(ok){
          if(!Number.isFinite(de)) de = e1 - e0
          if(!Number.isFinite(dn)) dn = n1 - n0
          if(!Number.isFinite(dz)) dz = z1 - z0
        }else{
          e1 = NaN; n1 = NaN; z1 = NaN
          de = NaN; dn = NaN; dz = NaN
        }

        let lin = toNum(t.linear)
        if(ok){
          if(!Number.isFinite(lin)) lin = Math.sqrt(de*de + dn*dn + dz*dz)
        }else{
          lin = NaN
        }

        out.push({ name, e0,n0,z0, e1,n1,z1, de,dn,dz, lin, ok })
      }
      return out
    }

    async function loadJsonByIdLog(idLog){
      const url = baseUrl + "beranda/get_deformasi_json/" + encodeURIComponent(idLog)
      const res = await fetch(url, { method:"GET", headers:{ "Accept":"application/json" } })
      if(!res.ok){
        const t = await res.text()
        throw new Error(t || ("HTTP " + res.status))
      }
      return await res.json()
    }

    let renderer, scene, camera, controls
    let groupAll, groupAxes, groupData
    let resizeObs = null

    function initThree(){
      renderer = new THREE.WebGLRenderer({ antialias:true, alpha:true })
      renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2))
      renderer.setClearColor(0xffffff, 1)
      elViewport.innerHTML = ""
      elViewport.appendChild(renderer.domElement)

      scene = new THREE.Scene()
      scene.background = new THREE.Color(0xffffff)

      camera = new THREE.PerspectiveCamera(55, 1, 0.01, 1e9)
      camera.position.set(8, 8, 6)

      controls = new OrbitControls(camera, renderer.domElement)
      controls.enableDamping = true
      controls.dampingFactor = 0.08
      controls.rotateSpeed = 0.6
      controls.zoomSpeed = 0.8
      controls.panSpeed = 0.8

      const amb = new THREE.AmbientLight(0xffffff, 0.85)
      scene.add(amb)
      const dir = new THREE.DirectionalLight(0xffffff, 0.65)
      dir.position.set(5, 10, 7)
      scene.add(dir)

      groupAll = new THREE.Group()
      groupAxes = new THREE.Group()
      groupData = new THREE.Group()
      groupAll.add(groupAxes)
      groupAll.add(groupData)
      scene.add(groupAll)

      const grid = new THREE.GridHelper(20, 20, 0xd1d5db, 0xe5e7eb)
      grid.material.opacity = 0.85
      grid.material.transparent = true
      groupAxes.add(grid)

      const ax = new THREE.AxesHelper(5)
      groupAxes.add(ax)

      resizeObs = new ResizeObserver(() => resize())
      resizeObs.observe(elViewport)

      animate()
      resize()
    }

    function resize(){
      if(!renderer || !camera) return
      const w = elViewport.clientWidth || 1
      const h = elViewport.clientHeight || 1
      renderer.setSize(w, h, false)
      camera.aspect = w / h
      camera.updateProjectionMatrix()
    }

    function animate(){
      requestAnimationFrame(animate)
      if(controls) controls.update()
      if(renderer && scene && camera) renderer.render(scene, camera)
    }

    function clearData(){
      if(!groupData) return
      while(groupData.children.length){
        const obj = groupData.children.pop()
        obj.traverse(n => {
          if(n.geometry) n.geometry.dispose()
          if(n.material){
            if(Array.isArray(n.material)) n.material.forEach(m => m.dispose())
            else n.material.dispose()
          }
        })
      }
    }

    function makeTextSprite(text, color="#0f172a"){
      const canvas = document.createElement("canvas")
      const ctx = canvas.getContext("2d")
      const fontSize = 64
      ctx.font = `700 ${fontSize}px ui-sans-serif, system-ui, -apple-system`
      const pad = 24
      const metrics = ctx.measureText(text)
      canvas.width = Math.ceil(metrics.width + pad*2)
      canvas.height = Math.ceil(fontSize + pad*2)

      ctx.font = `700 ${fontSize}px ui-sans-serif, system-ui, -apple-system`
      ctx.fillStyle = "rgba(255,255,255,0.85)"
      ctx.strokeStyle = "rgba(15,23,42,0.10)"
      ctx.lineWidth = 6
      ctx.beginPath()
      ctx.roundRect(3, 3, canvas.width-6, canvas.height-6, 24)
      ctx.fill()
      ctx.stroke()

      ctx.fillStyle = color
      ctx.textBaseline = "middle"
      ctx.fillText(text, pad, canvas.height/2)

      const tex = new THREE.CanvasTexture(canvas)
      tex.anisotropy = 4
      const mat = new THREE.SpriteMaterial({ map: tex, transparent:true })
      const spr = new THREE.Sprite(mat)
      const scale = 0.8
      spr.scale.set((canvas.width/canvas.height)*scale, 1*scale, 1)
      return spr
    }

    function fitCameraToBox(box){
      const size = new THREE.Vector3()
      const center = new THREE.Vector3()
      box.getSize(size)
      box.getCenter(center)

      const maxDim = Math.max(size.x, size.y, size.z, 1)
      const fov = camera.fov * (Math.PI/180)
      let camZ = Math.abs(maxDim / (2 * Math.tan(fov/2)))
      camZ *= 1.4

      const dir = new THREE.Vector3(1, 1, 0.75).normalize()
      camera.position.copy(center.clone().add(dir.multiplyScalar(camZ)))
      camera.near = Math.max(camZ / 1000, 0.01)
      camera.far = Math.max(camZ * 1000, 1000)
      camera.updateProjectionMatrix()

      controls.target.copy(center)
      controls.update()
    }

    function renderThree(points, meta){
      if(!renderer) initThree()
      clearData()

      const RTS_E = Number(elE.value)
      const RTS_N = Number(elN.value)
      const RTS_Z = Number(elZ.value)
      const vecScale = Number(elVecScale.value)
      const minLinear = Number(elMinLinear.value)

      const baseline = points
      const moved = points.filter(p => p.ok && Number.isFinite(p.lin) && p.lin >= minLinear)

      elHudStat.textContent = `Baseline: ${baseline.length} | OK: ${points.filter(p=>p.ok).length} | Render: ${moved.length}`

      const baseGeom = new THREE.SphereGeometry(0.08, 16, 16)
      const baseMat = new THREE.MeshStandardMaterial({ color: 0x334155, roughness:0.6, metalness:0.05 })
      const movedGeom = new THREE.SphereGeometry(0.11, 16, 16)
      const movedMat = new THREE.MeshStandardMaterial({ color: 0x2563eb, roughness:0.45, metalness:0.05 })
      const failMat = new THREE.MeshStandardMaterial({ color: 0x94a3b8, roughness:0.7, metalness:0.02 })

      const lineMat = new THREE.LineBasicMaterial({ color: 0x111827, transparent:true, opacity:0.55 })
      const arrowColor = 0x10b981

      const box = new THREE.Box3()

      for(const p of baseline){
        const m = new THREE.Mesh(baseGeom, p.ok ? baseMat : failMat)
        m.position.set(p.e0, p.n0, p.z0)
        groupData.add(m)
        box.expandByPoint(m.position)
      }

      for(const p of moved){
        const m1 = new THREE.Mesh(movedGeom, movedMat)
        m1.position.set(p.e1, p.n1, p.z1)
        groupData.add(m1)
        box.expandByPoint(m1.position)

        const pts = [ new THREE.Vector3(p.e0, p.n0, p.z0), new THREE.Vector3(p.e1, p.n1, p.z1) ]
        const geo = new THREE.BufferGeometry().setFromPoints(pts)
        const ln = new THREE.Line(geo, lineMat)
        groupData.add(ln)

        const dir = new THREE.Vector3(p.de, p.dn, p.dz)
        const len = dir.length()
        if(len > 0){
          dir.normalize()
          const L = len * vecScale
          const headLen = Math.max(L * 0.22, 0.15)
          const headWid = Math.max(L * 0.08, 0.06)
          const arr = new THREE.ArrowHelper(dir, new THREE.Vector3(p.e0, p.n0, p.z0), L, arrowColor, headLen, headWid)
          groupData.add(arr)
          box.expandByPoint(new THREE.Vector3(p.e0 + dir.x*L, p.n0 + dir.y*L, p.z0 + dir.z*L))
        }
      }

      const rtsGeom = new THREE.OctahedronGeometry(0.18)
      const rtsMat = new THREE.MeshStandardMaterial({ color: 0xef4444, roughness:0.45, metalness:0.05 })
      const rts = new THREE.Mesh(rtsGeom, rtsMat)
      rts.position.set(RTS_E, RTS_N, RTS_Z)
      groupData.add(rts)
      box.expandByPoint(rts.position)

      const rtsLabel = makeTextSprite("RTS", "#0f172a")
      rtsLabel.position.copy(rts.position.clone().add(new THREE.Vector3(0,0,0.35)))
      groupData.add(rtsLabel)

      if(!isFinite(box.min.x) || !isFinite(box.max.x)){
        box.setFromCenterAndSize(new THREE.Vector3(0,0,0), new THREE.Vector3(10,10,10))
      }

      const center = new THREE.Vector3()
      const size = new THREE.Vector3()
      box.getCenter(center)
      box.getSize(size)

      const diag = Math.sqrt(size.x*size.x + size.y*size.y + size.z*size.z)
      const Ldir = Math.max(diag * 0.10, 1)

      const nLine = new THREE.Line(
        new THREE.BufferGeometry().setFromPoints([center, center.clone().add(new THREE.Vector3(0, Ldir, 0))]),
        new THREE.LineBasicMaterial({ color: 0xb30000 })
      )
      const eLine = new THREE.Line(
        new THREE.BufferGeometry().setFromPoints([center, center.clone().add(new THREE.Vector3(Ldir, 0, 0))]),
        new THREE.LineBasicMaterial({ color: 0x111827 })
      )
      const sLine = new THREE.Line(
        new THREE.BufferGeometry().setFromPoints([center, center.clone().add(new THREE.Vector3(0, -Ldir, 0))]),
        new THREE.LineBasicMaterial({ color: 0x9ca3af })
      )
      const wLine = new THREE.Line(
        new THREE.BufferGeometry().setFromPoints([center, center.clone().add(new THREE.Vector3(-Ldir, 0, 0))]),
        new THREE.LineBasicMaterial({ color: 0x9ca3af })
      )

      groupData.add(nLine, eLine, sLine, wLine)

      const lblN = makeTextSprite("N", "#b30000"); lblN.position.copy(center.clone().add(new THREE.Vector3(0, Ldir*1.12, 0)))
      const lblE = makeTextSprite("E", "#111827"); lblE.position.copy(center.clone().add(new THREE.Vector3(Ldir*1.12, 0, 0)))
      const lblS = makeTextSprite("S", "#64748b"); lblS.position.copy(center.clone().add(new THREE.Vector3(0, -Ldir*1.12, 0)))
      const lblW = makeTextSprite("W", "#64748b"); lblW.position.copy(center.clone().add(new THREE.Vector3(-Ldir*1.12, 0, 0)))
      groupData.add(lblN, lblE, lblS, lblW)

      fitCameraToBox(box)

      const grid = groupAxes.children.find(x => x.type === "GridHelper")
      if(grid){
        const s = Math.max(diag * 0.6, 20)
        grid.scale.set(s/20, s/20, s/20)
      }

      const title = meta && meta.tanggal ? `RTS Deformasi 3D — ${meta.tanggal}` : "RTS Deformasi 3D"
      document.title = title
    }

    function isFullscreen(){ return !!document.fullscreenElement }
    async function toggleFullscreen(){
      if(!isFullscreen()){
        if(elFsTarget.requestFullscreen) await elFsTarget.requestFullscreen()
      }else{
        if(document.exitFullscreen) await document.exitFullscreen()
      }
    }
    function syncFsBtn(){
      elBtnFS.textContent = isFullscreen() ? "Exit Fullscreen" : "Fullscreen"
      resize()
    }
    document.addEventListener("fullscreenchange", syncFsBtn)
    elBtnFS.addEventListener("click", () => { toggleFullscreen().catch(()=>{}) })

    elBtnLoad.addEventListener("click", async () => {
      elLog.textContent = ""
      payload = null
      elBtnRender.disabled = true

      const idLog = elTanggal.value
      if(!idLog) return

      try{
        log("Loading...")
        payload = await loadJsonByIdLog(idLog)

        const rts = getRTSFromPayload(payload)
        if(rts){
          elE.value = rts.e
          elN.value = rts.n
          elZ.value = Number.isFinite(rts.z) ? rts.z : 0
        }

        const n = (payload && payload.data_pengukuran) ? payload.data_pengukuran.length : 0
        log("Loaded JSON from server")
        log(`tanggal: ${payload.tanggal || "-"}`)
        log(`rows: ${n}`)

        const pts = extractPoints(payload)
        log(`parsed prisms: ${pts.length}`)
        log(`valid shots: ${pts.filter(x=>x.ok).length}`)
        log(`failed shots: ${pts.filter(x=>!x.ok).length}`)

        elBtnRender.disabled = (pts.length === 0)
      }catch(e){
        log(String(e && e.message ? e.message : e))
      }
    })

    elBtnRender.addEventListener("click", () => {
      if(!payload) return
      const points = extractPoints(payload)
      if(points.length === 0){
        log("Tidak ada prisma yang kebaca untuk id_log ini.")
        return
      }
      renderThree(points, payload)
      resize()
    })

    window.addEventListener("load", () => {
      if(elTanggal && elTanggal.value){
        elBtnLoad.click()
      }
    })
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
