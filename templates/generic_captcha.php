<?php

return <<<VIEW
<input type="text" name="imagestring" size="20" value="" placeholder="Код подтверждения" />
<p>Пожалуйста, введите текст, изображенный на картинке внизу.<br />Этот процесс предотвращает автоматическую регистрацию.</p>
<img id="captcha" src="captcha_img.php?imagehash=$this->captcha_hash" alt="Captcha" ondblclick="document.getElementById('captcha').src = 'captcha_img.php?imagehash=$this->captcha_hash&amp;' + Math.random();" /><br />
<font color="red">Код чувствителен к регистру</font><br />Кликните два раза на картинке, чтобы обновить её.<input type="hidden" name="imagehash" value="$this->captcha_hash" />
VIEW;

?>