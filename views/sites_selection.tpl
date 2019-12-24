<div class="wrap">
    <h1>{{title}}</h1>
    <p>{'Please, select sites to use in testing.'}</p>
    <form method="post" action="options.php" novalidate="novalidate">
        {{wordpress_tokens}}

        <table class="form-table">
            <tbody>
            {% foreach sites as site %}
            <tr>
                <th scope="row"><label for="{{site.id}}">{{site.name}}</label></th>
                <td><input name="{{PLUGIN}}-sites[]" id="{{site.id}}" value="{{site.id}}"
                           {% if site.active %}checked="checked"{% endif %} type="checkbox"></td>
            </tr>
            {% endforeach %}
            </tbody>
        </table>

        <p class="submit"><input name="submit" id="submit" class="button button-primary" value="{'Save changes'}"
                                 type="submit"></p>
    </form>
</div>