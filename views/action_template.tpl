<form style="display:inline;"><!-- fix firefox bug with first form--></form>
<form action="{{link}}" id="{{form_id}}" method="{{method}}" target="_blank" style="display: inline;">
    <input type="hidden" name="{{input_name}}" value="{{item_url}}">
    <a href="javascript:{}" onclick="document.getElementById('{{form_id}}').submit(); return false;">{{short_name}}</a>
</form>