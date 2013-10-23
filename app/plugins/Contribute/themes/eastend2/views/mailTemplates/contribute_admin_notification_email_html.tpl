<?php

print "<p>"._t("There has been a new submission through the contribute form.  Please login to %1/admin to review the submission titled, '".$vs_record_name."'.", $this->request->config->get("site_host"))."</p>";
?>