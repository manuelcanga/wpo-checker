<div class="wrap">
    <h1>{{title}}</h1>
    <p>{'Please, select sites to use in your testing.'}</p>
    <form method="post" action="options.php" novalidate="novalidate">
        {{wordpress_tokens}}

        <table class="wp-list-table widefat  striped plugins">
            <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1">{'Check all'}</label>
                    <input id="cb-select-all-1" type="checkbox" title="{'Check all'}"/>
                </td>
                <th scope="col" id="name" class="manage-column column-name column-primary">{'Site'}</th>
                <th scope="col" id="description" class="manage-column column-description">{'Description'}</th>
            </tr>
            </thead>
            <tbody id="the-list">

            {% foreach sites as site %}
            <tr {% if site.active %}class="active"{% endif %} >
                <th scope="row" class="check-column">
                    <input name="{{PLUGIN}}-sites[]" id="{{site.id}}" value="{{site.id}}"
                           {% if site.active %}checked="checked"{% endif %} type="checkbox">
                </th>

                <td class="plugin-title column-primary">
                    {% if site.active %}<label for="{{site.id}}"><strong>{{site.name}}</strong></label>{% endif %}
                    {% if !site.active %}<label for="{{site.id}}">{{site.name}}</label>{% endif %}
                </td>

                <td class="column-description desc">
                    <div><p>{{site.description}}</p></div>
                </td>
            </tr>
            {% endforeach %}

            </tbody>
            <tfoot>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1">{'Check all'}</label>
                    <input id="cb-select-all-1" type="checkbox" title="{'Check all'}"/>
                </td>
                <th scope="col" id="name" class="manage-column column-name column-primary">{'Site'}</th>
                <th scope="col" id="description" class="manage-column column-description">{'Description'}</th>
            </tr>
            </tfoot>
        </table>

        <p class="submit"><input name="submit" id="submit" class="button button-primary" value="{'Save selection'}"
                                 type="submit"></p>
    </form>
</div>