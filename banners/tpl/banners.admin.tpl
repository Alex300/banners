<!-- BEGIN: MAIN -->
<div class="block button-toolbar">
    <a href="{PHP|cot_url('admin', 'm=other&p=banners')}" class="button">{PHP.L.ba_banners}</a>
    <a href="{PHP|cot_url('admin', 'm=structure&n=banners')}" class="button">{PHP.L.Categories}</a>
    <a href="{PHP|cot_url('admin', 'm=other&p=banners&n=clients')}" class="button">{PHP.L.ba_clients}</a>
    <a href="{PHP|cot_url('admin', 'm=other&p=banners&n=track')}" class="button">{PHP.L.ba_tracks}</a>
</div>

{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

{CONTENT}
<!-- END: MAIN -->
