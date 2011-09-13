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
	<li><a href="data/lexical_feature/list?max=50">Lexical Feature</a></li>
	<li><a href="data/lexical_data/list?max=500">Lexical Data</a></li>
	<li><a href="data/relationship_type/list">Relationship Types</a></li>
	<li><a href="data/semantic_field/list">Semantic Fields</a></li>
</ul>
<h2>Operations</h2>
<ul>
	<li><a href="operations/add_gdata">Add Grammatical Data</a></li>
	<li><a href="operations/add_ldata">Add Lexical Data</a></li>
</ul>
{/block}
