<?php
require('../../config.php');
require('../../utils/database.php');
session_start();

$conn = initialize_database();

$filter_tag = trim($_GET['tag'] ?? '');
$search     = trim($_GET['q']   ?? '');

$where = "WHERE deleted_at IS NULL";
if ($filter_tag !== '') {
    $where .= " AND tag = '" . mysqli_real_escape_string($conn, $filter_tag) . "'";
}
if ($search !== '') {
    $s      = mysqli_real_escape_string($conn, $search);
    $where .= " AND (title LIKE '%$s%' OR body LIKE '%$s%')";
}

$articles_res = mysqli_query($conn, "SELECT * FROM article $where ORDER BY created_at DESC");

$tags_res = mysqli_query($conn, "SELECT DISTINCT tag FROM article WHERE tag IS NOT NULL AND tag != '' AND deleted_at IS NULL ORDER BY tag");
$all_tags = [];
while ($t = mysqli_fetch_assoc($tags_res)) $all_tags[] = $t['tag'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Articles – PaddyCare</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/public/styles/styles.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/public/styles/fonts.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="shortcut icon" href="<?php echo BASE_URL; ?>/public/images/paddyCureLogo2.png" type="image/x-icon">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --forest:#1B3A2D;--deep:#122A20;--sage:#4A7C59;--moss:#6B9E6F;
  --lime:#A8C97F;--wheat:#E8D5A3;--straw:#F5EDD3;--white:#FAFDF7;--muted:#8BAF8E;
  --serif:'Playfair Display',Georgia,serif;--sans:'DM Sans',sans-serif;
}
html{scroll-behavior:smooth}
body{background:var(--white);color:var(--forest);font-family:var(--sans);font-weight:300;overflow-x:hidden}

.page-hero{background:var(--deep);padding:100px 8% 80px;position:relative;overflow:hidden}
.page-hero::before{content:'';position:absolute;inset:0;background-image:repeating-linear-gradient(160deg,rgba(107,158,111,.06) 0,rgba(107,158,111,.06) 1px,transparent 1px,transparent 60px);pointer-events:none}
.page-hero::after{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 60% 60% at 80% 50%,rgba(74,124,89,.2) 0%,transparent 70%);pointer-events:none}
.page-hero-content{position:relative;z-index:1}
.page-hero-label{font-size:10px;font-weight:500;letter-spacing:.28em;text-transform:uppercase;color:var(--lime);display:flex;align-items:center;gap:12px;margin-bottom:18px}
.page-hero-label::before{content:'';display:block;width:28px;height:1px;background:var(--lime)}
.page-hero-title{font-family:var(--serif);font-size:clamp(2.4rem,5vw,4rem);font-weight:700;color:var(--white);line-height:1.1;margin-bottom:16px}
.page-hero-title em{font-style:italic;color:var(--lime)}
.page-hero-sub{font-size:16px;line-height:1.75;color:rgba(250,253,247,.55);max-width:560px}

.articles-layout{display:grid;grid-template-columns:1fr 280px;gap:48px;max-width:1200px;margin:0 auto;padding:72px 8% 100px}

.filters-bar{display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin-bottom:40px}
.search-wrap{flex:1;min-width:200px;position:relative}
.search-wrap svg{position:absolute;left:13px;top:50%;transform:translateY(-50%);width:15px;height:15px;stroke:var(--muted);fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;pointer-events:none}
.search-wrap input{width:100%;border:1px solid #d8e8d0;border-radius:22px;padding:10px 16px 10px 38px;font-family:var(--sans);font-size:13px;color:var(--forest);outline:none;background:var(--white);transition:border-color .2s}
.search-wrap input:focus{border-color:var(--sage)}
.btn-search{background:var(--sage);color:var(--white);border:none;border-radius:22px;padding:10px 20px;font-family:var(--sans);font-size:12px;font-weight:500;cursor:pointer;transition:background .2s}
.btn-search:hover{background:var(--forest)}
.btn-clear{background:none;border:1px solid #d8e8d0;color:var(--muted);border-radius:22px;padding:10px 16px;font-family:var(--sans);font-size:12px;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center}

.articles-grid{display:flex;flex-direction:column;gap:32px}
.article-card{display:grid;grid-template-columns:260px 1fr;gap:0;border:1px solid #e0ead8;border-radius:10px;overflow:hidden;background:var(--white);transition:box-shadow .25s,transform .25s;text-decoration:none;color:inherit;cursor:pointer}
.article-card:hover{box-shadow:0 10px 40px rgba(27,58,45,.1);transform:translateY(-3px)}
.article-card-img{width:100%;height:200px;object-fit:cover;display:block;background:#e8eed4}
.article-card-img-placeholder{width:100%;height:200px;background:linear-gradient(135deg,#d4e6c3,#e8f0d8);display:flex;align-items:center;justify-content:center;font-size:2.5rem}
.article-card-body{padding:28px 30px;display:flex;flex-direction:column;justify-content:space-between}
.article-card-tag{font-size:9px;font-weight:500;letter-spacing:.18em;text-transform:uppercase;padding:4px 12px;border-radius:100px;display:inline-block;margin-bottom:12px;background:rgba(74,124,89,.1);color:var(--sage);border:1px solid rgba(74,124,89,.2);width:fit-content}
.article-card-title{font-family:var(--serif);font-size:1.25rem;font-weight:400;color:var(--forest);line-height:1.35;margin-bottom:10px}
.article-card-excerpt{font-size:13px;line-height:1.7;color:#5a7a60;margin-bottom:20px;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
.article-card-footer{display:flex;align-items:center;justify-content:space-between}
.article-card-date{font-size:11px;color:var(--muted);letter-spacing:.04em}
.article-read-more{font-size:12px;font-weight:500;color:var(--sage);display:inline-flex;align-items:center;gap:5px;transition:gap .2s,color .2s}
.article-card:hover .article-read-more{gap:9px;color:var(--forest)}
.article-read-more svg{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}

.no-articles{text-align:center;padding:80px 20px;color:var(--muted)}
.no-articles svg{width:48px;height:48px;stroke:rgba(74,124,89,.3);fill:none;stroke-width:1.2;stroke-linecap:round;stroke-linejoin:round;margin-bottom:14px}
.no-articles p{font-size:15px}

.articles-sidebar{display:flex;flex-direction:column;gap:28px}
.sidebar-card{background:var(--white);border:1px solid #e0ead8;border-radius:10px;overflow:hidden}
.sidebar-card-h{padding:16px 20px 12px;font-size:11px;font-weight:500;letter-spacing:.16em;text-transform:uppercase;color:var(--muted);border-bottom:1px solid #e0ead8}
.sidebar-card-b{padding:14px 20px 18px}
.sidebar-tags{display:flex;flex-wrap:wrap;gap:7px}
.sidebar-tag{font-size:11px;padding:5px 13px;border-radius:100px;background:rgba(74,124,89,.07);border:1px solid rgba(74,124,89,.15);color:#5a7a60;text-decoration:none;transition:background .2s,color .2s}
.sidebar-tag:hover,.sidebar-tag.active{background:var(--sage);color:var(--white);border-color:var(--sage)}
.recent-article{display:flex;gap:12px;align-items:flex-start;padding:10px 0;border-bottom:1px solid #f0f5ec}
.recent-article:last-child{border-bottom:none;padding-bottom:0}
.recent-thumb{width:52px;height:44px;object-fit:cover;border-radius:5px;background:#e8eed4;flex-shrink:0}
.recent-thumb-placeholder{width:52px;height:44px;border-radius:5px;background:linear-gradient(135deg,#d4e6c3,#e8f0d8);flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:1.2rem}
.recent-title{font-size:12px;font-weight:500;color:var(--forest);line-height:1.4;margin-bottom:3px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.recent-date{font-size:10px;color:var(--muted)}

/* MODAL */
.overlay{position:fixed;inset:0;z-index:1000;background:rgba(18,42,32,.85);backdrop-filter:blur(6px);display:flex;align-items:center;justify-content:center;padding:24px;opacity:0;pointer-events:none;transition:opacity .25s}
.overlay.open{opacity:1;pointer-events:all}
.art-modal{background:var(--white);border-radius:14px;width:100%;max-width:760px;max-height:90vh;overflow-y:auto;transform:translateY(24px) scale(.97);transition:transform .3s cubic-bezier(.34,1.2,.64,1)}
.overlay.open .art-modal{transform:translateY(0) scale(1)}
.modal-img{width:100%;max-height:320px;object-fit:cover;display:block;border-radius:14px 14px 0 0}
.modal-body{padding:36px 40px 44px}
.modal-tag{font-size:9px;font-weight:500;letter-spacing:.18em;text-transform:uppercase;padding:4px 12px;border-radius:100px;background:rgba(74,124,89,.1);color:var(--sage);border:1px solid rgba(74,124,89,.2);display:inline-block;margin-bottom:14px}
.modal-title{font-family:var(--serif);font-size:1.9rem;font-weight:700;color:var(--forest);line-height:1.2;margin-bottom:8px}
.modal-date{font-size:12px;color:var(--muted);margin-bottom:24px}
.modal-content{font-size:15px;line-height:1.85;color:#3a5a45;white-space:pre-wrap}
.modal-close{position:absolute;top:16px;right:16px;width:34px;height:34px;border-radius:50%;background:rgba(18,42,32,.7);border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--white);font-size:16px;transition:background .2s}
.modal-close:hover{background:var(--forest)}
.modal-top{position:relative}

@media(max-width:900px){.articles-layout{grid-template-columns:1fr}.articles-sidebar{display:none}.article-card{grid-template-columns:1fr}.article-card-img,.article-card-img-placeholder{height:180px}}
@media(max-width:600px){.modal-body{padding:24px 22px 32px}}
</style>
</head>
<body>

<?php include('../../components/header_navigation_bar.php'); ?>

<section class="page-hero">
  <div class="page-hero-content">
    <p class="page-hero-label">Knowledge Hub</p>
    <h1 class="page-hero-title">Paddy Farming <em>Articles</em></h1>
    <p class="page-hero-sub">Expert guides, disease treatment tips, and farming advice written for Sri Lankan paddy farmers.</p>
  </div>
</section>

<div class="articles-layout">
  <main class="articles-main">

    <form method="GET" class="filters-bar">
      <div class="search-wrap">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="q" placeholder="Search articles…" value="<?= htmlspecialchars($search) ?>">
      </div>
      <button type="submit" class="btn-search">Search</button>
      <?php if ($search || $filter_tag): ?>
        <button type="button" class="btn-clear" onclick="window.location.href='articles.php'">✕ Clear</button>
      <?php endif; ?>
    </form>

    <?php if ($filter_tag): ?>
    <div style="margin-bottom:24px">
      <span style="font-size:12px;color:var(--muted)">Filtered by:</span>
      <span style="font-size:12px;background:var(--sage);color:#fff;padding:4px 14px;border-radius:100px;margin-left:8px"><?= htmlspecialchars($filter_tag) ?></span>
    </div>
    <?php endif; ?>

    <div class="articles-grid">
    <?php if (mysqli_num_rows($articles_res) === 0): ?>
      <div class="no-articles">
        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        <p>No articles found<?= ($search||$filter_tag) ? ' — try a different search' : ' yet' ?>.</p>
      </div>
    <?php else:
      while ($a = mysqli_fetch_assoc($articles_res)):
        $excerpt = htmlspecialchars(mb_substr(strip_tags($a['body']), 0, 180)) . (mb_strlen($a['body']) > 180 ? '…' : '');
        $date    = date('M j, Y', strtotime($a['created_at']));
        $img_src = !empty($a['image']) ? BASE_URL . '/public/images/articles/' . htmlspecialchars($a['image']) : '';
    ?>
      <!-- data-* attributes hold all values safely — no inline JS string escaping -->
      <div class="article-card"
           data-title="<?= htmlspecialchars($a['title'], ENT_QUOTES) ?>"
           data-tag="<?= htmlspecialchars($a['tag'] ?? '', ENT_QUOTES) ?>"
           data-body="<?= htmlspecialchars($a['body'], ENT_QUOTES) ?>"
           data-img="<?= htmlspecialchars($img_src, ENT_QUOTES) ?>"
           data-date="<?= htmlspecialchars($date, ENT_QUOTES) ?>"
           onclick="openArticle(this)">

        <?php if ($img_src): ?>
          <img class="article-card-img" src="<?= $img_src ?>" alt="<?= htmlspecialchars($a['title']) ?>">
        <?php else: ?>
          <div class="article-card-img-placeholder">🌾</div>
        <?php endif; ?>

        <div class="article-card-body">
          <?php if ($a['tag']): ?>
            <span class="article-card-tag"><?= htmlspecialchars($a['tag']) ?></span>
          <?php endif; ?>
          <h2 class="article-card-title"><?= htmlspecialchars($a['title']) ?></h2>
          <p class="article-card-excerpt"><?= $excerpt ?></p>
          <div class="article-card-footer">
            <span class="article-card-date"><?= $date ?></span>
            <span class="article-read-more">
              Read Article
              <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </span>
          </div>
        </div>
      </div>
    <?php endwhile; endif; ?>
    </div>
  </main>

  <aside class="articles-sidebar">
    <?php if (!empty($all_tags)): ?>
    <div class="sidebar-card">
      <div class="sidebar-card-h">Browse Topics</div>
      <div class="sidebar-card-b">
        <div class="sidebar-tags">
          <a href="articles.php" class="sidebar-tag <?= $filter_tag==='' ? 'active' : '' ?>">All</a>
          <?php foreach ($all_tags as $t): ?>
            <a href="?tag=<?= urlencode($t) ?>" class="sidebar-tag <?= $filter_tag===$t ? 'active' : '' ?>"><?= htmlspecialchars($t) ?></a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <?php
    $recent_res = mysqli_query($conn, "SELECT id, title, image, created_at FROM article WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 5");
    $recent_articles = [];
    while ($r = mysqli_fetch_assoc($recent_res)) $recent_articles[] = $r;
    ?>
    <?php if (!empty($recent_articles)): ?>
    <div class="sidebar-card">
      <div class="sidebar-card-h">Recent Articles</div>
      <div class="sidebar-card-b">
        <?php foreach ($recent_articles as $r):
          $r_img  = !empty($r['image']) ? BASE_URL . '/public/images/articles/' . htmlspecialchars($r['image']) : '';
          $r_date = date('M j, Y', strtotime($r['created_at']));
        ?>
        <div class="recent-article">
          <?php if ($r_img): ?>
            <img class="recent-thumb" src="<?= $r_img ?>" alt="">
          <?php else: ?>
            <div class="recent-thumb-placeholder">🌾</div>
          <?php endif; ?>
          <div>
            <div class="recent-title"><?= htmlspecialchars($r['title']) ?></div>
            <div class="recent-date"><?= $r_date ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <div class="sidebar-card">
      <div class="sidebar-card-h">Quick Tips</div>
      <div class="sidebar-card-b" style="display:flex;flex-direction:column;gap:14px">
        <?php foreach ([
          ['🌿','Early detection','Check your paddy fields at least twice a week.'],
          ['💧','Proper irrigation','Avoid waterlogging — it encourages fungal growth.'],
          ['🔬','Use AI detection','Upload a leaf photo for instant diagnosis.'],
        ] as $tip): ?>
        <div style="display:flex;gap:10px;align-items:flex-start">
          <span style="font-size:18px;flex-shrink:0"><?= $tip[0] ?></span>
          <div>
            <div style="font-size:12px;font-weight:500;color:var(--forest);margin-bottom:2px"><?= $tip[1] ?></div>
            <div style="font-size:11px;color:#5a7a60;line-height:1.5"><?= $tip[2] ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </aside>
</div>

<!-- ARTICLE MODAL -->
<div class="overlay" id="overlay">
  <div class="art-modal">
    <div class="modal-top">
      <img id="modal-img" src="" alt="" class="modal-img" style="display:none">
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <div class="modal-body">
      <span id="modal-tag" class="modal-tag" style="display:none"></span>
      <h2 id="modal-title" class="modal-title"></h2>
      <p id="modal-date" class="modal-date"></p>
      <div id="modal-content" class="modal-content"></div>
    </div>
  </div>
</div>

<?php include('../../components/footer.php'); ?>

<script>
// Read from data-* attributes — handles quotes, newlines, special chars safely
function openArticle(card) {
  const title = card.dataset.title;
  const tag   = card.dataset.tag;
  const body  = card.dataset.body;
  const img   = card.dataset.img;
  const date  = card.dataset.date;

  document.getElementById('modal-title').textContent   = title;
  document.getElementById('modal-date').textContent    = date;
  document.getElementById('modal-content').textContent = body;

  const tagEl = document.getElementById('modal-tag');
  if (tag) { tagEl.textContent = tag; tagEl.style.display = 'inline-block'; }
  else      { tagEl.style.display = 'none'; }

  const imgEl = document.getElementById('modal-img');
  if (img) { imgEl.src = img; imgEl.style.display = 'block'; }
  else      { imgEl.style.display = 'none'; }

  document.getElementById('overlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  document.getElementById('overlay').classList.remove('open');
  document.body.style.overflow = '';
}

document.getElementById('overlay').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>
</body>
</html>