{extends file="layout.tpl"}

{block name="content"}
<h2>{$main_title}</h2>
<ul>
	<li><a href="data/case_study_area/list">Case Study Area</a></li>
	<li><a href="data/language/list?max=50">Languages</a></li>
	<li><a href="data/language_family/list">Language Families</a></li>
	<li><a href="data/language_subgroup/list">Language Subgroups</a></li>
	<li><a href="data/grammatical_feature/list?max=50">Grammatical Feature</a></li>
	<li><a href="data/grammatical_feature_category/list">Grammatical Feature Category</a></li>
	<li><a href="data/language_to_grammatical_feature/list?max=100">Grammatical Data</a></li>
</ul>
{/block}
