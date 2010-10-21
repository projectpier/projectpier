<?php if (isset($favorite_companies) && is_array($favorite_companies) && count($favorite_companies)) { ?>
<h2><?php echo lang('favorite companies'); ?></h2>
<ul>
<?php foreach ($favorite_companies as $company) { ?>
  <li><a href="<?php echo $company->getCardUrl(); ?>"><?php echo $company->getName()?></a></li>
<?php } // foreach ?>
</ul>
<?php } // if ?>