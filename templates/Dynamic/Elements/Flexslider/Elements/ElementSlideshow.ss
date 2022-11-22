<% if $Title && $ShowTitle %><h2 class="element__title">$Title</h2><% end_if %>
<% if $Content %><div class="element__content">$Content</div><% end_if %>

<div class="row element__slideshow__list">
    <div class="col-md-12">
        <% if $SlideShow %>
            <% include Includes/FlexSlider %>
        <% end_if %>
    </div>
</div>
