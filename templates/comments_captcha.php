<?php

return <<<VIEW
<input type="text" name="imagestring" size="20" value="" placeholder="��� �������������" />
<p>����������, ������� �����, ������������ �� �������� �����.<br />���� ������� ������������� �������������� �����������.</p>
<img id="captcha" src="captcha_img.php?imagehash=$this->captcha_hash" alt="Captcha" ondblclick="document.getElementById('captcha').src = 'captcha_img.php?imagehash=$this->captcha_hash&amp;' + Math.random();" /><br />
<font color="red">��� ������������ � ��������</font><br />�������� ��� ���� �� ��������, ����� �������� �.<input type="hidden" name="imagehash" value="$this->captcha_hash" />
VIEW;

?>