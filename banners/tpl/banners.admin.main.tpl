<!-- BEGIN: MAIN -->

<!-- IF {PAGE_TITLE} -->
<h2 class="tags">{PAGE_TITLE}</h2>
<!-- ENDIF -->

{PHP.L.Filters}:
<form method="get" action="{LIST_URL}">
    {PHP.L.Title}: <input type="text" name="fil[title]" value="{FILTER_VALUES.title}" />
    {PHP.L.Category}: {FILTER_CATEGORY}
    {PHP.L.ba_client}: {FILTER_CLIENT}
    {PHP.L.ba_published}: {FILTER_PUBLISHED}
    <div style="text-align: right">
        <input type="hidden" name="m" value="{PHP.m}">
        <input type="hidden" name="p" value="{PHP.p}">
        {PHP.L.adm_sort}: {SORT_BY} {SORT_WAY}
    </div>
    <input type="submit" value="{PHP.L.Show}" />
</form>

<table class="cells margintop10">
    <tr>
        <td class="coltop"></td>
        <td class="coltop">{PHP.L.Title}</td>
        <td class="coltop">{PHP.L.Category}</td>
        <td class="coltop">{PHP.L.ba_sticky}</td>
        <td class="coltop">{PHP.L.ba_published}</td>
        <td class="coltop">{PHP.L.ba_client}</td>
        <td class="coltop">{PHP.L.ba_impressions}</td>
        <td class="coltop">{PHP.L.ba_clicks_all}</td>
        <td class="coltop">{PHP.L.Edit}</td>
        <td class="coltop">{PHP.L.Delete}</td>
        <td class="coltop">ID</td>
    </tr>
    <!-- BEGIN: LIST_ROW -->
    <tr>
        <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_NUM}</td>
        <td class="{LIST_ROW_ODDEVEN}"><a href="{LIST_ROW_EDIT_URL}">{LIST_ROW_TITLE}</a></td>
        <td class="{LIST_ROW_ODDEVEN}">{LIST_ROW_CATEGORY_TITLE}</td>
        <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_STICKY_TEXT}</td>
        <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_PUBLISHED}</td>
        <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_CLIENT_TITLE}</td>
        <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_IMPMADE} / {LIST_ROW_IMPTOTAL_TEXT}</td>
        <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_CLICKS} / {LIST_ROW_CLICKS_PERSENT}</td>
        <td class="{LIST_ROW_ODDEVEN} centerall">
            <a href="{LIST_ROW_ID|cot_url('admin', 'm=other&p=banners&a=edit&id=$this')}"><img src="images/icons/default/arrow-follow.png" /></a>
        </td>
        <td class="{LIST_ROW_ODDEVEN} centerall">
            <a href="{LIST_ROW_DELETE_URL}" class="confirmLink"><img src="images/icons/default/delete.png" /></a>
        </td>
        <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_ID}</td>
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

<a href="{PHP|cot_url('admin', 'm=other&p=banners&a=edit')}" class="button">{PHP.L.Add}</a>
<!-- END: MAIN -->

<!-- BEGIN: EDIT -->
<!-- IF {PAGE_TITLE} -->
<h2 class="tags">{PAGE_TITLE}</h2>
<!-- ENDIF -->

<!-- BEGIN: FORM -->
<form action="{FORM_ID|cot_url('admin', 'm=other&p=banners&a=edit&id=$this')}" method="POST" ENCTYPE="multipart/form-data">
   <!-- <input type="hidden" name="rid" value="{FORM_ID}" /> -->
    <input type="hidden" name="act" value="save" />

    <table class="cells">
        <tr>
            <td class="width20">{PHP.L.Title}:</td>
            <td>{FORM_TITLE}</td>
        </tr>
        <tr>
            <td>{PHP.L.Category}:</td>
            <td>{FORM_CATEGORY}</td>
        </tr>
        <tr>
            <td>{PHP.L.Type}:</td>
            <td>{FORM_TYPE}</td>
        </tr>
        <tr>
            <td>{PHP.L.Image}:</td>
            <td>
                {BANNER_IMAGE}
                {FORM_FILE}
            </td>
        </tr>
        <tr>
            <td>{PHP.L.ba_width}:</td>
            <td>{FORM_WIDTH} {PHP.L.ba_for_file_only}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_height}:</td>
            <td>{FORM_HEIGHT} {PHP.L.ba_for_file_only}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_alt}:</td>
            <td>{FORM_ALT}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_custom_code}:</td>
            <td>{FORM_CUSTOMCODE}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_click_url}:</td>
            <td>{FORM_CLICKURL}</td>
        </tr>
        <tr>
            <td>{PHP.L.Description}:</td>
            <td>{FORM_DESCRIPTION}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_sticky}?:</td>
            <td>{FORM_STICKY}<br />{PHP.L.ba_sticky_tip}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_publish_up}:</td>
            <td>{FORM_PUBLISH_UP}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_publish_down}:</td>
            <td>{FORM_PUBLISH_DOWN}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_imptotal}:</td>
            <td>{FORM_IMPTOTAL} 0 - {PHP.L.ba_unlimited}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_impmade}:</td>
            <td>{FORM_IMPMADE}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_clicks_all}:</td>
            <td>{FORM_CLICKS}</td>
        </tr>
        <tr>
            <td>{PHP.L.ba_client}:</td>
            <td>{FORM_CLIENT_ID}</td>
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