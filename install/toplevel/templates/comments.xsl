<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>
<!-- xslt for reporting comments presented in a single table displaying only the 
	 category and positive or negative rating, can parse multiple comments for 
	 each subject and combine them; written comments are not displayed -->

<xsl:output method='html' />

<xsl:template match='text()' />

<xsl:key name='catname' match='cattable/catnames' use='.'/>
<xsl:variable name="student" select="//student"/>

<xsl:template match="/">
	  <xsl:apply-templates select="div"/>
</xsl:template>

<xsl:template match="div">
<xsl:for-each select="$student">


  <div>

	<div id="logo">
	  <img src="../images/schoollogo.png" width="130px"/>
	</div>

	<div class='studenthead'>
	  <h1>Monthly Progress Report</h1>
	  <table id='studentdata'>
		<tr>
		  <td>
			<label>
			  Student
			</label>
			<xsl:value-of select="forename/value/text()" />&#160;
			<xsl:value-of select="middlenames/value/text()" />&#160;
			<xsl:value-of select="surname/value/text()" />
		  </td>
		</tr>
		<tr>		
		  <td>
			<label>
			  Form
			</label>
			<xsl:value-of select="registrationgroup/value/text()" />
			<xsl:text>&#160;&#160;&#160;&#160;&#160;&#160;</xsl:text>
			<label>Date</label>
			<xsl:value-of select="publishdate/text()" />
		  </td>
		</tr>
	  </table>
	</div>

	<div class='spacer'></div>

 	<xsl:variable name="catname" select="cattable/catnames"/>
 	<xsl:variable name="comments" select="comments"/>

	<div>
	  <table class="grades">
		<tr>
		  <th>
			<xsl:text></xsl:text>
		  </th>
		  <xsl:for-each select="$catname">
			<th>
			  <xsl:value-of select="."/>
			</th>
		  </xsl:for-each>
		</tr>
		<xsl:for-each select="$comments">
		  <xsl:sort select="subject/value" order="ascending"/>
		  <xsl:variable name="subname" select="subject/value"/>
		  <xsl:variable name="subid" select="subject/id"/>
		  <xsl:if test="generate-id(.)=generate-id($comments[subject/id=$subid])">

			<tr>
			  <th>
				<xsl:value-of select="$subname"/>
			  </th>
			  <td>
				<xsl:choose>
				  <xsl:when test="categories/category[label=$catname[1] and (rating/value&lt;0 or rating/value='N')]">
					<xsl:text>Poor</xsl:text>
				  </xsl:when>
				  <xsl:when test="categories/category[label=$catname[1] and (rating/value&gt;0)]">
					<xsl:text>Good</xsl:text>
				  </xsl:when>
				  <xsl:when test="../comments[categories/category/label=$catname[1] and (rating/value&lt;0 or rating/value='N') and subject/id=$subid]">
					<xsl:text>Poor</xsl:text>
				  </xsl:when>
				  <xsl:otherwise>
					<xsl:text>-</xsl:text>
				  </xsl:otherwise>
				</xsl:choose>
			  </td>
			  <td>
				<xsl:choose>
				  <xsl:when test="categories/category[label=$catname[2] and (rating/value&lt;0 or rating/value='N')]">
					<xsl:text>Poor</xsl:text>
				  </xsl:when>
				  <xsl:when test="categories/category[label=$catname[2] and (rating/value&gt;0)]">
					<xsl:text>Good</xsl:text>
				  </xsl:when>
				  <xsl:when test="../comments[categories/category/label=$catname[2] and (rating/value&lt;0 or rating/value='N') and subject/id=$subid] ">
					<xsl:text>Poor</xsl:text>
				  </xsl:when>
				  <xsl:otherwise>
					<xsl:text>-</xsl:text>
				  </xsl:otherwise>
				</xsl:choose>
			  </td>
			  <td>
				<xsl:choose>
				  <xsl:when test="categories/category[label=$catname[3] and (rating/value&lt;0 or rating/value='N')]">
					<xsl:text>Poor</xsl:text>
				  </xsl:when>
				  <xsl:when test="categories/category[label=$catname[3] and (rating/value&gt;0)]">
					<xsl:text>Good</xsl:text>
				  </xsl:when>
				  <xsl:when test="../comments[categories/category/label=$catname[3] and (rating/value&lt;0 or rating/value='N') and subject/id=$subid] ">
					<xsl:text>Poor</xsl:text>
				  </xsl:when>
				  <xsl:otherwise>
					<xsl:text>-</xsl:text>
				  </xsl:otherwise>
				</xsl:choose>
			  </td>
			  <td>
				<xsl:choose>
				  <xsl:when test="categories/category[label=$catname[4] and (rating/value&lt;0 or rating/value='N')]">
					<xsl:text>Poor</xsl:text>
				  </xsl:when>
				  <xsl:when test="categories/category[label=$catname[4] and (rating/value&gt;0)]">
					<xsl:text>Good</xsl:text>
				  </xsl:when>
				  <xsl:when test="../comments[categories/category/label=$catname[4] and (rating/value&lt;0 or rating/value='N') and subject/id=$subid] ">
					<xsl:text>Poor</xsl:text>
				  </xsl:when>
				  <xsl:otherwise>
					<xsl:text>-</xsl:text>
				  </xsl:otherwise>
				</xsl:choose>
			  </td>
			  <td>
				<xsl:choose>
				  <xsl:when test="categories/category[label=$catname[5] and (rating/value&lt;0 or rating/value='N')]">
					<xsl:text>Poor</xsl:text>
				  </xsl:when>
				  <xsl:when test="categories/category[label=$catname[5] and (rating/value&gt;0)]">
					<xsl:text>Good</xsl:text>
				  </xsl:when>
				  <xsl:when test="../comments[categories/category/label=$catname[5] and (rating/value&lt;0 or rating/value='N') and subject/id=$subid] ">
					<xsl:text>Poor</xsl:text>
				  </xsl:when>
				  <xsl:otherwise>
					<xsl:text>-</xsl:text>
				  </xsl:otherwise>
				</xsl:choose>
			  </td>
			</tr>
		  </xsl:if>
		</xsl:for-each>
	  </table>
	</div>

	<div class='comment'>
	  Comments
	</div>

	<div class='tinycomment'>
	Year Coordinator<br />
	<p class='signature'>Signed</p>
  </div>

  <div class='tinycomment'>
	<p class='signature'>Signed</p>
  </div>

</div>
<hr />
</xsl:for-each>
</xsl:template>

</xsl:stylesheet>
