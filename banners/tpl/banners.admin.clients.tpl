<!-- BEGIN: MAIN -->

<!-- IF {PAGE_TITLE} -->
<h2 class="tags">{PAGE_TITLE}</h2>
<!-- ENDIF -->

<table class="cells">
    <tr>
        <td class="coltop"></td>
        <td class="coltop">{PHP.L.Title}</td>
        <td class="coltop">{PHP.L.ba_purchase_type}</td>
        <td class="coltop">{PHP.L.ba_published}</td>
        <td class="coltop">{PHP.L.Edit}</td>
        <td class="coltop">{PHP.L.Delete}</td>
        <td class="coltop">ID</td>
    </tr>
    <!-- BEGIN: LIST_ROW -->
    <tr>
        <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_NUM}</td>
        <td class="{LIST_ROW_ODDEVEN}"><a href="{LIST_ROW_URL}">{LIST_ROW_TITLE}</a></td>
        <td class="{LIST_ROW_ODDEVEN}">{LIST_ROW_PURCHASE_TEXT}</td>
        <td class="{LIST_ROW_ODDEVEN} centerall">{LIST_ROW_PUBLISHED}</td>
        <td class="{LIST_ROW_ODDEVEN} centerall">
            <a href="{LIST_ROW_ID|cot_url('admin', 'm=other&p=banners&n=clients&a=edit&id=$this')}"><img src="images/icons/default/arrow-follow.png" /></a>
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