<?php
require_once '../Controller/comment_handler.php';
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($product->name) ?> | Navyrost</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/css/clothStyle.css" />
    <link rel="stylesheet" href="/css/cloth.css" />
</head>
<body>
    <?php require_once '../blocks/header.php'; ?>
    
    <main class="product-page">
        <div class="product-container">
            <div class="product-gallery">
                <div class="main-image">
                    <img src="/<?= htmlspecialchars($product->image) ?>" alt="<?= htmlspecialchars($product->name) ?>" id="main-image" />
                </div>
                <div class="thumbnail-container">
                    <div class="thumbnail active" onclick="changeImage('/<?= htmlspecialchars($product->image) ?>')">
                        <img src="/<?= htmlspecialchars($product->image) ?>" alt="Мініатюра 1" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                </div>
            </div>
            
            <div class="product-info">
                <h1 class="product-title"><?= htmlspecialchars($product->name) ?></h1>
                
                <?php if ($commentsCount > 0): ?>
                <div class="product-rating">
                    <div class="rating-stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?php if ($i <= floor($averageRating)): ?>
                                ★
                            <?php elseif ($i == ceil($averageRating) && $averageRating - floor($averageRating) >= 0.5): ?>
                                ☆
                            <?php else: ?>
                                ☆
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <span class="product-rating-value"><?= number_format($averageRating, 1) ?></span>
                    <span class="product-rating-count">(<?= $commentsCount ?> відгуків)</span>
                </div>
                <?php endif; ?>
                
                <div class="product-price"><?= number_format($product->price, 0, ',', ' ') ?> грн.</div>
                
                <div class="delivery-info">
                    <div><i class="fas fa-check"></i> Безкоштовна доставка Новою поштою</div>
                    <div><i class="far fa-heart"></i> Обране</div>
                </div>
                
                <div class="size-selection">
                    <h3>Оберіть розмір</h3>
                    <table class="size-table">
                        <tr>
                            <th>U.S.</th>
                            <th>UA</th>
                            <th>EU</th>
                            <th>UK</th>
                            <th>CM</th>
                        </tr>
                        <?php foreach ($product->size as $index => $size): ?>
                        <tr>
                            <td class="<?= $index === 0 ? 'selected' : '' ?>" onclick="selectSize(this)"><?= htmlspecialchars($size) ?></td>
                            <td onclick="selectSize(this)"><?= htmlspecialchars($size) ?></td>
                            <td onclick="selectSize(this)"><?= htmlspecialchars($size) ?></td>
                            <td onclick="selectSize(this)"><?= htmlspecialchars($size) ?></td>
                            <td onclick="selectSize(this)"><?= htmlspecialchars($size) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

                <form method="POST" action="/Controller/cart-handler.php">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id" value="<?= $product->id ?>">
                    <input type="hidden" name="name" value="<?= htmlspecialchars($product->name) ?>">
                    <input type="hidden" name="price" value="<?= $product->price ?>">
                    <input type="hidden" name="image" value="<?= htmlspecialchars($product->image) ?>">
                    <input type="hidden" name="size" id="selected-size" value="<?= htmlspecialchars($product->size[0] ?? '') ?>">
                    <button type="submit" class="add-to-cart">ДОДАТИ В КОШИК</button>
                </form>
                
                <div class="additional-info">
                    <div><strong>Наявність у магазинах</strong></div>
                    <div><strong>Доставка</strong></div>
                    <div><strong>Повернення</strong></div>
                    
                    <div class="info-links">
                        <a href="#">Таблиця розмірів</a>
                        <a href="#">Оплата</a>
                        <a href="#reviews">Відгуки</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="product-details">
            <h3><?= htmlspecialchars($product->name) ?></h3>
            <p><?= htmlspecialchars($product->description) ?></p>
            
            <h4>Характеристики:</h4>
            <ul>
                <li><strong>Бренд:</strong> <?= htmlspecialchars($product->brand) ?></li>
                <li><strong>Категорія:</strong> <?= htmlspecialchars($product->category) ?></li>
                <li><strong>Стать:</strong> <?= htmlspecialchars($product->gender) ?></li>
                <li><strong>Розміри:</strong> <?= htmlspecialchars(implode(', ', $product->size)) ?></li>
             <li><strong>Колір:</strong> 
            <?php 
            foreach ($product->color as $colorName) {
                $colorName = trim(mb_strtolower($colorName));
                $hexCode = $colorCodes[$colorName] ?? '#CCCCCC'; 
                echo '<span style="display: inline-block; margin-right: 10px;">';
                echo htmlspecialchars($colorName) . ' <span style="display: inline-block; width: 12px; height: 12px; background-color: ' . $hexCode . '; border: 1px solid #ddd;"></span> ' . $hexCode;
                echo '</span>';
            }
            ?>
           </li>
            </ul>
        </div>

<div class="comments-section" id="reviews">
    <h3>Відгуки про товар</h3>
    
    <?php if (isset($_SESSION['comment_success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['comment_success'] ?>
            <?php unset($_SESSION['comment_success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['comment_error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['comment_error'] ?>
            <?php unset($_SESSION['comment_error']); ?>
        </div>
    <?php endif; ?>
    
    <div class="comment-form">
        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST">
                <textarea name="comment_text" placeholder="Напишіть ваш відгук..." required></textarea>
                <div class="rating-stars">
                    <span>Оцінка (необов'язково):</span>
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>">
                        <label for="star<?= $i ?>">★</label>
                    <?php endfor; ?>
                </div>
                <button type="submit" name="add_comment" class="add-to-cart">Відправити відгук</button>
            </form>
        <?php else: ?>
            <div class="login-prompt">
                <p>Щоб залишити відгук, будь ласка <a href="/View/login.php">увійдіть</a> або <a href="/register.php">зареєструйтесь</a></p>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="comments-list">
        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <div class="comment-header">
                    <span class="user-name"><?= htmlspecialchars($comment['firstname'] . ' ' . $comment['lastname']) ?></span>
                    <span class="comment-date"><?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?></span>
                    <?php if ($comment['rating']): ?>
                        <div class="comment-rating"><?= str_repeat('★', $comment['rating']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="comment-text"><?= nl2br(htmlspecialchars($comment['text'])) ?></div>
                
                <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $comment['user_id'] || ($_SESSION['user_role'] ?? '') === 'admin')): ?>
                <form method="POST" action="/Controller/comment_handler.php" class="delete-comment-form">
                    <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                    <button type="submit" name="delete_comment" class="delete-comment">Видалити</button>
                </form>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Ще немає відгуків про цей товар. Будьте першим!</p>
        <?php endif; ?>
    </div>
</div>
    </main>

    <?php require_once '../blocks/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/script/script.js"></script>
</body>
</html>