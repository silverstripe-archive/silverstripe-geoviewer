<% if Message %>
<div class='message'>$Message</div>
<% else %>
<div class='featureInfoContent'>
	<h2>$Layer.Title</h2>	
	<h4>Feature: $FeatureIDs</h4>
	<ul>
	<% control Items %>
		<li>$attributeName : $attributeValue</li>
	<% end_control %>
	</ul>
</div>
<% end_if %>
