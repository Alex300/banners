<!-- BEGIN: MAIN -->

<!-- IF {PAGE_TITLE} -->
<h2 class="tags">{PAGE_TITLE}</h2>
<!-- ENDIF -->

{PHP.L.Filters}:
<form method="get" action="{LIST_URL}">
    {PHP.L.Title}: <input type="text" name="fil[title]" value="{FILTER_VALUES.title}" />
    {PHP.L.Category}: {FILTER_CATEGORY}
    {PHP.L.ba_client}: {FILTER_CLIENT}
    {PHP.L.Type}: {FILTER_TRACK_TYPE}<br />
    {PHP.L.Date} {PHP.L.ba_from}: {FILTER_DATE_FROM} {PHP.L.ba_to} {FILTER_DATE_TO}
    <div style="text-align: right">
        <input type="hidden" name="m" value="{PHP.m}">
        <input type="hidden" name="p" value="{PHP.p}">
        <input type="hidden" name="n" value="{PHP.n}">
        {PHP.L.adm_sort}: {SORT_BY} {SORT_WAY}
    </div>
    <input type="submit" value="{PHP.L.Show}" />
    <button id="clearStats" class="button" style="float: right" name="a" value="clear"
            onclick="return confirm('{PHP.L.ba_clear_tracks_param_confirm}')"><img
                src="images/icons/default/delete.png" style="vertical-align: middle" />
        {PHP.L.ba_clear_tracks_param}</button>
</form>

<table class="cells margintop10">
    <tr>
        <td class="coltop"></td>
        <td class="coltop">{PHP.L.Title}</td>
        <td class="coltop">{PHP.L.ba_client}</td>
        <td class="coltop">{PHP.L.Type}</td>
        <td class="coltop">{PHP.L.Count}</td>
        <td class="coltop">{PHP.L.Date}</td>
    </tr>
    <!-- BEGIN: LIST_ROW -->
    <tr>
        <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_NUM}</td>
        <td class="{LIST_ROW_ODDEVEN}">
            <a href="{LIST_ROW_EDIT_URL}">{LIST_ROW_TITLE}</a>
            <div class="desc">{LIST_ROW_CATEGORY_TITLE}</div>
        </td>
        <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_CLIENT_TITLE}</td>
        <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_TRACK_TYPE_TEXT}</td>
        <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_TRACK_COUNT}</td>
        <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_TRACK_DATE}</td>
    </tr>
    <!-- END: LIST_ROW -->

    <!-- IF {LIST_TOTALLINES} == '0' -->
    <tr>
        <td class="odd centerall" colspan="12">{PHP.L.None}</td>
    </tr>
    <!-- ENDIF -->

</table>

<!-- IF {LIST_CURRENTPAGE} -->
<div class="paging">
    {LIST_PAGEPREV}{LIST_PAGINATION}{LIST_PAGENEXT}<span>{PHP.L.Total}: {LIST_TOTALLINES},
        {PHP.L.Onpage}: {LIST_ITEMS_ON_PAGE}</span>
</div>
<!-- ENDIF -->

<a href="{PHP|cot_url('admin', 'm=other&p=banners&n=clients&a=edit')}" class="button">{PHP.L.Add}</a>
<!-- END: MAIN -->

<!-- BEGIN: EDIT -->
<!-- IF {PAGE_TITLE} -->
<h2 class="tags">{PAGE_TITLE}</h2>
<!-- ENDIF -->

<!-- BEGIN: FORM -->
<form action="{FORM_ID|cot_url('admin', 'm=other&p=banners&n=clients&a=edit&id=$this')}" method="POST">
    <input type="hidden" name="act" value="save" />

    <table class="cells">
        <tr>
            <td class="width20">{PHP.L.Title}:</td>
            <td>{FORM_TITLE}</td>
        </tr>
        <tr>
            <td>{PHP.L.Email}:</td>
            <td>{FORM_EMAIL}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_purchase_type}:</td>
            <td>{FORM_PURCHASE_TYPE}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_track_impressions}:</td>
            <td>{FORM_TRACK_IMP} <br />{PHP.L.ba_track_impressions_tip}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_track_clicks}:</td>
            <td>{FORM_TRACK_CLICKS} <br />{PHP.L.ba_track_clicks_tip}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_extrainfo}:</td>
            <td>{FORM_EXTRAINFO}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_published}?:</td>
            <td>{FORM_PUBLISHED}</td>
        </tr>
    </table>

    <input type="submit" value="{PHP.L.Submit}" />

    <!-- IF {FORM_ID} > 0 -->
    <a href="{FORM_DELETE_URL}" class="confirmLink button"><img src="images/icons/default/delete.png" style="vertical-align: middle;" />
    {PHP.L.Delete}</a>
    <!-- ENDIF -->
</form>
<!-- END: FORM -->


<!-- END: EDIT -->