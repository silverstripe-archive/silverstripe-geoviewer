<div class='bubblePopup'>
	<\% if Message \%><div class='bubbleContent'>$Message</div><\% end_if \%>

	<h2>\$Layer.Title</h2>	

	<\% loop Features \%>
	<div class='bubbleContent'>
		<ul><% with FeatureType %><% loop LabelsForTheTemplate %>
			<li class='row <% if Odd %>odd<% end_if %>'>
				<span class='label'>$Label</span><p class='value'>$RemoteColumnName.RAW</p>
			</li><% end_loop %><% end_with %>
		</ul>
	</div>
	<\% end_loop \%>
</div>